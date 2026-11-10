const { app, BrowserWindow, ipcMain, dialog } = require('electron');
const path = require('path');
const fs = require('fs');
const mysql = require('mysql2/promise');
const xlsx = require('xlsx');
const { Client } = require('ssh2');
const { calculateMedicalScore } = require('./arabicNLP');

let mainWindow;
const configPath = path.join(app.getPath('userData'), 'db-config.json');

// Global SSH tunnel variables
let activeSshClient = null;
let activeTunnelConfigKey = '';
const localTunnelPort = 33066;

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1280,
        height: 800,
        title: 'جمعية الفجر الشبابي الفلسطيني FPYA - نظام التدقيق والتحليل الذكي',
        icon: path.join(__dirname, 'logo.ico'),
        webPreferences: {
            nodeIntegration: true,
            contextIsolation: false
        }
    });

    mainWindow.removeMenu();
    mainWindow.loadFile('index.html');

    // Open devtools if in development mode
    // mainWindow.webContents.openDevTools();
}

app.whenReady().then(() => {
    createWindow();

    app.on('activate', function () {
        if (BrowserWindow.getAllWindows().length === 0) createWindow();
    });
});

app.on('window-all-closed', function () {
    if (activeSshClient) {
        activeSshClient.end();
    }
    if (process.platform !== 'darwin') app.quit();
});

// Helper function to establish SSH tunnel
async function establishTunnel(config) {
    if (!config.useSshTunnel || !config.ssh) {
        if (activeSshClient) {
            activeSshClient.end();
            activeSshClient = null;
            activeTunnelConfigKey = '';
        }
        return config;
    }

    const configKey = `${config.ssh.host}:${config.ssh.port}:${config.ssh.username}:${config.ssh.password}`;
    if (activeSshClient && activeTunnelConfigKey === configKey) {
        return {
            ...config,
            host: '127.0.0.1',
            port: localTunnelPort
        };
    }

    if (activeSshClient) {
        activeSshClient.end();
        activeSshClient = null;
        activeTunnelConfigKey = '';
    }

    return new Promise((resolve, reject) => {
        const sshClient = new Client();

        sshClient.on('ready', () => {
            const localPort = localTunnelPort;
            const remotePort = parseInt(config.port) || 3306;
            const remoteHost = '127.0.0.1';

            const net = require('net');
            const localServer = net.createServer((sock) => {
                sshClient.forwardOut('127.0.0.1', sock.remotePort, remoteHost, remotePort, (err, stream) => {
                    if (err) {
                        sock.destroy();
                        return;
                    }
                    sock.pipe(stream);
                    stream.pipe(sock);
                });
            });

            localServer.on('error', (err) => {
                sshClient.end();
                reject(new Error(`Local tunnel server error: ${err.message}`));
            });

            localServer.listen(localPort, '127.0.0.1', () => {
                activeSshClient = {
                    client: sshClient,
                    server: localServer,
                    end: () => {
                        try { localServer.close(); } catch (e) { }
                        try { sshClient.end(); } catch (e) { }
                    }
                };
                activeTunnelConfigKey = configKey;

                resolve({
                    ...config,
                    host: '127.0.0.1',
                    port: localPort
                });
            });
        });

        sshClient.on('error', (err) => {
            reject(new Error(`SSH connection error: ${err.message}`));
        });

        sshClient.connect({
            host: config.ssh.host,
            port: parseInt(config.ssh.port) || 22,
            username: config.ssh.username,
            password: config.ssh.password,
            readyTimeout: 20000
        });
    });
}

// Helper function to get database connection
async function getDbConnection(config) {
    let finalConfig = config;
    if (config.useSshTunnel) {
        finalConfig = await establishTunnel(config);
    } else {
        if (activeSshClient) {
            activeSshClient.end();
            activeSshClient = null;
            activeTunnelConfigKey = '';
        }
    }

    return await mysql.createConnection({
        host: finalConfig.host,
        port: parseInt(finalConfig.port) || 3306,
        user: finalConfig.username,
        password: finalConfig.password,
        database: finalConfig.database,
        connectTimeout: 15000
    });
}

// Helper to parse .env files
function parseEnvFile(filePath) {
    if (!fs.existsSync(filePath)) return null;
    try {
        const content = fs.readFileSync(filePath, 'utf8');
        const lines = content.split(/\r?\n/);
        const env = {};
        lines.forEach(line => {
            // Match key=value ignoring comments
            const match = line.match(/^\s*([\w.-]+)\s*=\s*(.*)?\s*$/);
            if (match) {
                let key = match[1];
                let value = match[2] || '';
                // Strip comments if any
                if (value.includes('#')) {
                    value = value.split('#')[0].trim();
                }
                // Strip quotes
                if (value.startsWith('"') && value.endsWith('"')) value = value.slice(1, -1);
                if (value.startsWith("'") && value.endsWith("'")) value = value.slice(1, -1);
                env[key] = value.trim();
            }
        });
        return env;
    } catch (e) {
        return null;
    }
}

// 1. IPC connection handlers
ipcMain.handle('load-connection-config', () => {
    let config = null;

    // Attempt to load previously saved config in userData
    if (fs.existsSync(configPath)) {
        try {
            config = JSON.parse(fs.readFileSync(configPath, 'utf8'));
        } catch (e) { }
    }

    if (!config) {
        config = {
            host: '46.202.158.78', // Force online server IP
            port: '3306',
            database: '',
            username: '',
            password: ''
        };
    }

    // Always attempt to parse Laravel's parent .env file to extract credentials
    const envPaths = [
        path.join(__dirname, '..', '.env'),
        'D:\\wamp64\\www\\fajr-palastine\\.env'
    ];

    for (const envPath of envPaths) {
        const env = parseEnvFile(envPath);
        if (env) {
            config.host = '46.202.158.78'; // Override host to the requested server host IP
            if (env.DB_PORT) config.port = env.DB_PORT;
            if (env.DB_DATABASE) config.database = env.DB_DATABASE;
            if (env.DB_USERNAME) config.username = env.DB_USERNAME;
            if (env.DB_PASSWORD) config.password = env.DB_PASSWORD;
            break;
        }
    }

    return config;
});

ipcMain.handle('test-connection', async (event, config) => {
    let conn;
    try {
        conn = await getDbConnection(config);
        await conn.ping();
        return { success: true };
    } catch (err) {
        return { success: false, error: err.message };
    } finally {
        if (conn) await conn.end();
    }
});

ipcMain.handle('save-connection-config', (event, config) => {
    try {
        fs.writeFileSync(configPath, JSON.stringify(config, null, 2), 'utf8');
        return { success: true };
    } catch (err) {
        return { success: false, error: err.message };
    }
});

// 2. IPC data loaders
ipcMain.handle('fetch-initial-data', async (event, config) => {
    let conn;
    try {
        conn = await getDbConnection(config);

        // Fetch areas
        const [areas] = await conn.execute('SELECT id, name FROM area_responsibles ORDER BY name ASC');

        return { success: true, areas };
    } catch (err) {
        return { success: false, error: err.message };
    } finally {
        if (conn) await conn.end();
    }
});

// 3. Main scoring engine
ipcMain.handle('run-audit', async (event, { dbConfig, auditSettings, filters }) => {
    let conn;
    try {
        conn = await getDbConnection(dbConfig);



        const areaIds = filters.areaIds.map(Number);
        if (areaIds.length === 0) {
            return { success: false, error: 'الرجاء اختيار منطقة واحدة على الأقل' };
        }

        // 1. Fetch heads of families
        let headsQuery = `
            SELECT p.*, a.name as area_name 
            FROM persons p
            LEFT JOIN area_responsibles a ON p.area_responsible_id = a.id
            WHERE p.relative_id IS NULL AND p.area_responsible_id IN (${areaIds.join(',')})
        `;
        const params = [];

        const [heads] = await conn.execute(headsQuery, params);
        if (heads.length === 0) {
            return { success: true, families: [], stats: { total: 0, A: 0, B: 0, C: 0, D: 0 } };
        }

        const headIdNums = heads.map(h => h.id_num).filter(Boolean);

        // 2. Fetch family members
        let members = [];
        if (headIdNums.length > 0) {
            const placeholders = headIdNums.map(() => '?').join(',');
            const [membersRows] = await conn.query(
                `SELECT * FROM persons WHERE relative_id IN (${placeholders})`,
                headIdNums
            );
            members = membersRows;
        }

        // 3. Fetch distribution history (optimized to filter only selected heads)
        const headIds = heads.map(h => h.id).filter(Boolean);
        let historyRows = [];
        if (headIds.length > 0) {
            const placeholders = headIds.map(() => '?').join(',');
            const [rows] = await conn.query(`
                SELECT pb.person_id, 
                       COUNT(*) as times, 
                       SUM(pb.quantity) as total_qty, 
                       MAX(pb.delivery_date) as last_delivery
                FROM project_beneficiaries pb
                WHERE pb.status = 'مستلم' AND pb.person_id IN (${placeholders})
                GROUP BY pb.person_id
            `, headIds);
            historyRows = rows;
        }
        const historyMap = new Map();
        historyRows.forEach(row => {
            historyMap.set(row.person_id, row);
        });

        // Group members by relative_id for rapid O(1) retrieval
        const membersMap = new Map();
        members.forEach(m => {
            if (!membersMap.has(m.relative_id)) {
                membersMap.set(m.relative_id, []);
            }
            membersMap.get(m.relative_id).push(m);
        });

        // 4. Perform Scoring Calculation
        const results = [];
        const stats = { total: 0, A: 0, B: 0, C: 0, D: 0 };

        const ageThreshold = parseInt(auditSettings.age_threshold) || 60;
        const infantScore = parseInt(auditSettings.infant_score) || 18;
        const childScore = parseInt(auditSettings.child_score) || 10;
        const tentScore = parseInt(auditSettings.tent_score) || 15;
        const destroyedScore = parseInt(auditSettings.destroyed_score) || 20;
        const catAMin = parseInt(auditSettings.cat_a_min) || 85;
        const catBMin = parseInt(auditSettings.cat_b_min) || 60;
        const catCMin = parseInt(auditSettings.cat_c_min) || 35;
        const customKeywords = auditSettings.custom_keywords || '';
        const forceIncapacitated = auditSettings.force_incapacitated !== false;
        const checkAdultSon = auditSettings.check_adult_son !== false;

        const currentYear = new Date().getFullYear();

        // Calculate age helper
        const getAge = (dobString) => {
            if (!dobString) return 0;
            try {
                const dob = new Date(dobString);
                if (!isNaN(dob.getTime())) {
                    return currentYear - dob.getFullYear();
                }
                const year = parseInt(dobString.substring(0, 4));
                if (!isNaN(year)) {
                    return currentYear - year;
                }
            } catch (e) { }
            return 0;
        };

        for (const head of heads) {
            const familyMembers = membersMap.get(head.id_num) || [];
            let score = 0;
            let reasons = [];

            // Primary breadwinners evaluation (Head & Spouse)
            const primaryHeads = [head];
            const spouse = familyMembers.find(m => ['زوج', 'زوجة', 'husband', 'wife'].includes(m.relationship?.toLowerCase()));
            if (spouse) primaryHeads.push(spouse);

            let hasHealthyParent = false;
            let isParentIncapacitated = false;

            for (const p of primaryHeads) {
                const medical = calculateMedicalScore(p.condition_description, customKeywords);
                const age = getAge(p.dob);
                const isElderly = age >= ageThreshold;

                if (medical.score > 0 || isElderly) {
                    score += medical.score > 0 ? medical.score : 35;
                    const label = (p.relationship === 'زوجة' || p.gender === 'أنثى') ? 'الأم' : 'الأب';
                    reasons.push(isElderly ? `كبر سن ${label} (${age} سنة)` : `إصابة ${label} (${medical.match})`);
                    isParentIncapacitated = true;
                } else {
                    hasHealthyParent = true;
                }
            }

            // Find eldest adult son (18+) as substitute breadwinner
            let eldestSon = null;
            let eldestSonAge = 0;
            for (const m of familyMembers) {
                if (['ابن', 'son'].includes(m.relationship?.toLowerCase()) && ['ذكر', 'male'].includes(m.gender?.toLowerCase())) {
                    const mAge = getAge(m.dob);
                    if (mAge >= 18 && mAge > eldestSonAge) {
                        eldestSon = m;
                        eldestSonAge = mAge;
                    }
                }
            }

            let hasHealthyProvider = hasHealthyParent;
            if (checkAdultSon && eldestSon && !hasHealthyParent) {
                const sonMedical = calculateMedicalScore(eldestSon.condition_description, customKeywords);
                if (sonMedical.score > 0) {
                    score += sonMedical.score;
                    reasons.push(`عجز الابن المعيل البديل (${sonMedical.match})`);
                } else {
                    hasHealthyProvider = true;
                    reasons.push(`وجود ابن بالغ معيل (${eldestSonAge} سنة)`);
                }
            }

            const noProvider = (isParentIncapacitated || !hasHealthyParent) && !hasHealthyProvider;
            if (forceIncapacitated && noProvider) {
                score = Math.max(score, catAMin);
                reasons.push('الأسرة تحت الأولوية القصوى (عجز المعيل وبلا بديل)');
            }

            // Kids needs
            let infantsCount = 0;
            let childrenCount = 0;
            for (const m of familyMembers) {
                const age = getAge(m.dob);
                if (age > 0) {
                    if (age < 2) {
                        infantsCount++;
                        score += infantScore;
                    } else if (age < 5) {
                        childrenCount++;
                        score += childScore;
                    }
                }
            }
            if (infantsCount > 0) reasons.push(`احتياج حليب وبامبرز (${infantsCount})`);
            if (childrenCount > 0) reasons.push(`أطفال دون سن 5 سنوات (${childrenCount})`);

            // Health status of dependents
            for (const m of familyMembers) {
                // Skip spouse since they are evaluated in primaryHeads
                if (spouse && m.id === spouse.id) continue;
                const mMedical = calculateMedicalScore(m.condition_description, customKeywords);
                if (mMedical.score > 0) {
                    score += (mMedical.score * 0.3);
                    reasons.push(`إصابة تابع (${m.first_name}: ${mMedical.match})`);
                }
            }

            // Housing damage and shelter
            if (['خيمة', 'مركز إيواء'].includes(head.housing_type)) {
                score += tentScore;
            }
            if (head.housing_damage_status === 'مدمر كلي') {
                score += destroyedScore;
            }

            // Inject a tiny unique factor based on ID to avoid duplicate scores
            const uniqueFactor = (head.id_num % 1000) / 10000;
            const finalScore = Math.min(score + uniqueFactor, 100);

            // Determine category
            let category = 'D';
            let badge = 'success';
            if (finalScore >= catAMin) {
                category = 'A';
                badge = 'danger';
            } else if (finalScore >= catBMin) {
                category = 'B';
                badge = 'warning';
            } else if (finalScore >= catCMin) {
                category = 'C';
                badge = 'info';
            }

            stats[category]++;
            stats.total++;

            // Get delivery details
            const deliveryInfo = historyMap.get(head.id);
            const times = deliveryInfo ? deliveryInfo.times : 0;
            const totalQty = deliveryInfo ? parseFloat(deliveryInfo.total_qty) : 0;
            const lastDelivery = deliveryInfo ? deliveryInfo.last_delivery : 'لم يستلم أبداً';

            const membersCount = familyMembers.length + 1;

            results.push({
                id: head.id,
                id_num: head.id_num,
                name: `${head.first_name} ${head.family_name}`,
                full_name: `${head.first_name} ${head.father_name || ''} ${head.grandfather_name || ''} ${head.family_name}`.replace(/\s+/g, ' ').trim(),
                phone: head.phone || 'غير متوفر',
                members_count: membersCount,
                score: Math.round(finalScore * 100) / 100,
                category,
                badge,
                reasons: reasons.length > 0 ? reasons.join(' • ') : 'حالة مستقرة',
                address: head.area_name || 'غير محدد',
                times_served: times,
                total_qty: totalQty,
                last_delivery: lastDelivery
            });
        }

        // Sort descending by score
        results.sort((a, b) => b.score - a.score);

        return { success: true, families: results, stats };
    } catch (err) {
        return { success: false, error: err.message };
    } finally {
        if (conn) await conn.end();
    }
});

ipcMain.handle('run-individual-audit', async (event, { dbConfig, idNum, auditSettings }) => {
    let conn;
    try {
        conn = await getDbConnection(dbConfig);

        // 1. Fetch person
        const [personRows] = await conn.execute('SELECT * FROM persons WHERE id_num = ?', [idNum]);
        if (personRows.length === 0) {
            return { success: true, found: false };
        }

        const person = personRows[0];
        let headIdNum = person.id_num;
        let isDependent = false;

        // If this is a dependent, fetch the head of family
        if (person.relative_id) {
            isDependent = true;
            headIdNum = person.relative_id;
        }

        // Fetch head
        const [heads] = await conn.execute(`
            SELECT p.*, a.name as area_name 
            FROM persons p
            LEFT JOIN area_responsibles a ON p.area_responsible_id = a.id
            WHERE p.id_num = ? AND p.relative_id IS NULL
        `, [headIdNum]);

        if (heads.length === 0) {
            // Fallback: if no head found with that relative_id, treat the person as the head
            const [fallbackHeads] = await conn.execute(`
                SELECT p.*, a.name as area_name 
                FROM persons p
                LEFT JOIN area_responsibles a ON p.area_responsible_id = a.id
                WHERE p.id_num = ?
            `, [person.id_num]);
            if (fallbackHeads.length === 0) {
                return { success: true, found: false };
            }
            heads.push(fallbackHeads[0]);
            isDependent = false;
        }

        const head = heads[0];

        // Fetch family members
        const [familyMembers] = await conn.execute(
            `SELECT * FROM persons WHERE relative_id = ?`,
            [head.id_num]
        );

        // Fetch delivery history for the head
        const [historyRows] = await conn.query(`
            SELECT pb.person_id, 
                   COUNT(*) as times, 
                   SUM(pb.quantity) as total_qty, 
                   MAX(pb.delivery_date) as last_delivery
            FROM project_beneficiaries pb
            WHERE pb.status = 'مستلم' AND pb.person_id = ?
            GROUP BY pb.person_id
        `, [head.id]);

        const deliveryInfo = historyRows[0];

        // Scoring rules
        const ageThreshold = parseInt(auditSettings.age_threshold) || 60;
        const infantScore = parseInt(auditSettings.infant_score) || 18;
        const childScore = parseInt(auditSettings.child_score) || 10;
        const tentScore = parseInt(auditSettings.tent_score) || 15;
        const destroyedScore = parseInt(auditSettings.destroyed_score) || 20;
        const catAMin = parseInt(auditSettings.cat_a_min) || 85;
        const catBMin = parseInt(auditSettings.cat_b_min) || 60;
        const customKeywords = auditSettings.custom_keywords || '';
        const forceIncapacitated = auditSettings.force_incapacitated !== false;
        const checkAdultSon = auditSettings.check_adult_son !== false;

        const currentYear = new Date().getFullYear();
        const getAge = (dobString) => {
            if (!dobString) return 0;
            try {
                const dob = new Date(dobString);
                if (!isNaN(dob.getTime())) return currentYear - dob.getFullYear();
                const year = parseInt(dobString.substring(0, 4));
                if (!isNaN(year)) return currentYear - year;
            } catch (e) { }
            return 0;
        };

        let score = 0;
        let reasons = [];

        // Primary breadwinners
        const primaryHeads = [head];
        const spouse = familyMembers.find(m => ['زوج', 'زوجة', 'husband', 'wife'].includes(m.relationship?.toLowerCase()));
        if (spouse) primaryHeads.push(spouse);

        let hasHealthyParent = false;
        let isParentIncapacitated = false;

        for (const p of primaryHeads) {
            const medical = calculateMedicalScore(p.condition_description, customKeywords);
            const age = getAge(p.dob);
            const isElderly = age >= ageThreshold;

            if (medical.score > 0 || isElderly) {
                score += medical.score > 0 ? medical.score : 35;
                const label = (p.relationship === 'زوجة' || p.gender === 'أنثى') ? 'الأم' : 'الأب';
                reasons.push(isElderly ? `كبر سن ${label} (${age} سنة)` : `إصابة ${label} (${medical.match})`);
                isParentIncapacitated = true;
            } else {
                hasHealthyParent = true;
            }
        }

        // Eldest adult son
        let eldestSon = null;
        let eldestSonAge = 0;
        for (const m of familyMembers) {
            if (['ابن', 'son'].includes(m.relationship?.toLowerCase()) && ['ذكر', 'male'].includes(m.gender?.toLowerCase())) {
                const mAge = getAge(m.dob);
                if (mAge >= 18 && mAge > eldestSonAge) {
                    eldestSon = m;
                    eldestSonAge = mAge;
                }
            }
        }

        let hasHealthyProvider = hasHealthyParent;
        if (checkAdultSon && eldestSon && !hasHealthyParent) {
            const sonMedical = calculateMedicalScore(eldestSon.condition_description, customKeywords);
            if (sonMedical.score > 0) {
                score += sonMedical.score;
                reasons.push(`عجز الابن المعيل البديل (${sonMedical.match})`);
            } else {
                hasHealthyProvider = true;
                reasons.push(`وجود ابن بالغ معيل (${eldestSonAge} سنة)`);
            }
        }

        const noProvider = (isParentIncapacitated || !hasHealthyParent) && !hasHealthyProvider;
        if (forceIncapacitated && noProvider) {
            score = Math.max(score, catAMin);
            reasons.push('الأسرة تحت الأولوية القصوى (عجز المعيل وبلا بديل)');
        }

        // Kids
        let infantsCount = 0;
        let childrenCount = 0;
        for (const m of familyMembers) {
            const age = getAge(m.dob);
            if (age > 0) {
                if (age < 2) {
                    infantsCount++;
                    score += infantScore;
                } else if (age < 5) {
                    childrenCount++;
                    score += childScore;
                }
            }
        }
        if (infantsCount > 0) reasons.push(`احتياج حليب وبامبرز (${infantsCount})`);
        if (childrenCount > 0) reasons.push(`أطفال دون سن 5 سنوات (${childrenCount})`);

        // Health of dependents
        for (const m of familyMembers) {
            if (spouse && m.id === spouse.id) continue;
            const mMedical = calculateMedicalScore(m.condition_description, customKeywords);
            if (mMedical.score > 0) {
                score += (mMedical.score * 0.3);
                reasons.push(`إصابة تابع (${m.first_name}: ${mMedical.match})`);
            }
        }

        // Housing
        if (['خيمة', 'مركز إيواء'].includes(head.housing_type)) {
            score += tentScore;
        }
        if (head.housing_damage_status === 'مدمر كلي') {
            score += destroyedScore;
        }

        const uniqueFactor = (head.id_num % 1000) / 10000;
        const finalScore = Math.min(score + uniqueFactor, 100);

        let category = 'D';
        let badge = 'success';
        if (finalScore >= catAMin) {
            category = 'A';
            badge = 'danger';
        } else if (finalScore >= catBMin) {
            category = 'B';
            badge = 'warning';
        } else if (finalScore >= catCMin) {
            category = 'C';
            badge = 'info';
        }

        const times = deliveryInfo ? deliveryInfo.times : 0;
        const totalQty = deliveryInfo ? parseFloat(deliveryInfo.total_qty) : 0;
        const lastDelivery = deliveryInfo ? deliveryInfo.last_delivery : 'لم يستلم أبداً';

        const familyObj = {
            id: head.id,
            id_num: head.id_num,
            full_name: `${head.first_name} ${head.father_name || ''} ${head.grandfather_name || ''} ${head.family_name}`.replace(/\s+/g, ' ').trim(),
            members_count: familyMembers.length + 1,
            score: Math.round(finalScore * 100) / 100,
            category,
            badge,
            reasons: reasons.length > 0 ? reasons.join(' • ') : 'حالة مستقرة (المعيل سليم، السكن غير مدمر، لا يوجد مرضى أو أطفال دون 5 سنوات)',
            address: head.area_name || 'غير محدد',
            times_served: times,
            total_qty: totalQty,
            last_delivery: lastDelivery
        };

        return { success: true, found: true, family: familyObj, isDependent };
    } catch (err) {
        return { success: false, error: err.message };
    } finally {
        if (conn) await conn.end();
    }
});

ipcMain.handle('run-batch-audit', async (event, { dbConfig, idNums, auditSettings }) => {
    let conn;
    try {
        conn = await getDbConnection(dbConfig);

        const validIds = idNums.filter(id => id && id.toString().length >= 8);
        if (validIds.length === 0) {
            return { success: false, error: 'أرقام الهويات غير صالحة' };
        }

        const placeholders = validIds.map(() => '?').join(',');

        // 1. Fetch persons (they could be heads or dependents)
        const [persons] = await conn.query(`SELECT id_num, relative_id FROM persons WHERE id_num IN (${placeholders})`, validIds);

        if (persons.length === 0) {
            return { success: true, families: [], stats: { total: 0, A: 0, B: 0, C: 0, D: 0 } };
        }

        // Find the actual heads of families for all these persons
        const headIdsToFetch = new Set();
        for (const p of persons) {
            if (p.relative_id) {
                headIdsToFetch.add(p.relative_id);
            } else {
                headIdsToFetch.add(p.id_num);
            }
        }

        const headIdsArray = Array.from(headIdsToFetch);
        const headPlaceholders = headIdsArray.map(() => '?').join(',');

        // 2. Fetch heads
        const [heads] = await conn.query(`
            SELECT p.*, a.name as area_name 
            FROM persons p
            LEFT JOIN area_responsibles a ON p.area_responsible_id = a.id
            WHERE p.id_num IN (${headPlaceholders}) AND p.relative_id IS NULL
        `, headIdsArray);

        if (heads.length === 0) {
            return { success: true, families: [], stats: { total: 0, A: 0, B: 0, C: 0, D: 0 } };
        }

        const headIdNums = heads.map(h => h.id_num).filter(Boolean);
        const headIds = heads.map(h => h.id).filter(Boolean);

        // 3. Fetch family members
        let members = [];
        if (headIdNums.length > 0) {
            const hPlaceholders = headIdNums.map(() => '?').join(',');
            const [membersRows] = await conn.query(
                `SELECT * FROM persons WHERE relative_id IN (${hPlaceholders})`,
                headIdNums
            );
            members = membersRows;
        }

        // 4. Fetch distribution history
        let historyRows = [];
        if (headIds.length > 0) {
            const hPlaceholders = headIds.map(() => '?').join(',');
            const [rows] = await conn.query(`
                SELECT pb.person_id, 
                       COUNT(*) as times, 
                       SUM(pb.quantity) as total_qty, 
                       MAX(pb.delivery_date) as last_delivery
                FROM project_beneficiaries pb
                WHERE pb.status = 'مستلم' AND pb.person_id IN (${hPlaceholders})
                GROUP BY pb.person_id
            `, headIds);
            historyRows = rows;
        }
        const historyMap = new Map();
        historyRows.forEach(row => historyMap.set(row.person_id, row));

        const membersMap = new Map();
        members.forEach(m => {
            if (!membersMap.has(m.relative_id)) membersMap.set(m.relative_id, []);
            membersMap.get(m.relative_id).push(m);
        });

        const results = [];
        const stats = { total: 0, A: 0, B: 0, C: 0, D: 0 };

        const ageThreshold = parseInt(auditSettings.age_threshold) || 60;
        const infantScore = parseInt(auditSettings.infant_score) || 18;
        const childScore = parseInt(auditSettings.child_score) || 10;
        const tentScore = parseInt(auditSettings.tent_score) || 15;
        const destroyedScore = parseInt(auditSettings.destroyed_score) || 20;
        const catAMin = parseInt(auditSettings.cat_a_min) || 85;
        const catBMin = parseInt(auditSettings.cat_b_min) || 60;
        const catCMin = parseInt(auditSettings.cat_c_min) || 35;
        const customKeywords = auditSettings.custom_keywords || '';
        const forceIncapacitated = auditSettings.force_incapacitated !== false;
        const checkAdultSon = auditSettings.check_adult_son !== false;

        const currentYear = new Date().getFullYear();
        const getAge = (dobString) => {
            if (!dobString) return 0;
            try {
                const dob = new Date(dobString);
                if (!isNaN(dob.getTime())) return currentYear - dob.getFullYear();
                const year = parseInt(dobString.substring(0, 4));
                if (!isNaN(year)) return currentYear - year;
            } catch (e) { }
            return 0;
        };

        for (const head of heads) {
            const familyMembers = membersMap.get(head.id_num) || [];
            let score = 0;
            let reasons = [];

            const primaryHeads = [head];
            const spouse = familyMembers.find(m => ['زوج', 'زوجة', 'husband', 'wife'].includes(m.relationship?.toLowerCase()));
            if (spouse) primaryHeads.push(spouse);

            let hasHealthyParent = false;
            let isParentIncapacitated = false;

            for (const p of primaryHeads) {
                const medical = calculateMedicalScore(p.condition_description, customKeywords);
                const age = getAge(p.dob);
                const isElderly = age >= ageThreshold;

                if (medical.score > 0 || isElderly) {
                    score += medical.score > 0 ? medical.score : 35;
                    const label = (p.relationship === 'زوجة' || p.gender === 'أنثى') ? 'الأم' : 'الأب';
                    reasons.push(isElderly ? `كبر سن ${label} (${age} سنة)` : `إصابة ${label} (${medical.match})`);
                    isParentIncapacitated = true;
                } else {
                    hasHealthyParent = true;
                }
            }

            let eldestSon = null;
            let eldestSonAge = 0;
            for (const m of familyMembers) {
                if (['ابن', 'son'].includes(m.relationship?.toLowerCase()) && ['ذكر', 'male'].includes(m.gender?.toLowerCase())) {
                    const mAge = getAge(m.dob);
                    if (mAge >= 18 && mAge > eldestSonAge) {
                        eldestSon = m;
                        eldestSonAge = mAge;
                    }
                }
            }

            let hasHealthyProvider = hasHealthyParent;
            if (checkAdultSon && eldestSon && !hasHealthyParent) {
                const sonMedical = calculateMedicalScore(eldestSon.condition_description, customKeywords);
                if (sonMedical.score > 0) {
                    score += sonMedical.score;
                    reasons.push(`عجز الابن المعيل البديل (${sonMedical.match})`);
                } else {
                    hasHealthyProvider = true;
                    reasons.push(`وجود ابن بالغ معيل (${eldestSonAge} سنة)`);
                }
            }

            const noProvider = (isParentIncapacitated || !hasHealthyParent) && !hasHealthyProvider;
            if (forceIncapacitated && noProvider) {
                score = Math.max(score, catAMin);
                reasons.push('الأسرة تحت الأولوية القصوى (عجز المعيل وبلا بديل)');
            }

            let infantsCount = 0;
            let childrenCount = 0;
            for (const m of familyMembers) {
                const age = getAge(m.dob);
                if (age > 0) {
                    if (age < 2) { infantsCount++; score += infantScore; }
                    else if (age < 5) { childrenCount++; score += childScore; }
                }
            }
            if (infantsCount > 0) reasons.push(`احتياج حليب وبامبرز (${infantsCount})`);
            if (childrenCount > 0) reasons.push(`أطفال دون سن 5 سنوات (${childrenCount})`);

            for (const m of familyMembers) {
                if (spouse && m.id === spouse.id) continue;
                const mMedical = calculateMedicalScore(m.condition_description, customKeywords);
                if (mMedical.score > 0) {
                    score += (mMedical.score * 0.3);
                    reasons.push(`إصابة تابع (${m.first_name}: ${mMedical.match})`);
                }
            }

            if (['خيمة', 'مركز إيواء'].includes(head.housing_type)) score += tentScore;
            if (head.housing_damage_status === 'مدمر كلي') score += destroyedScore;

            const uniqueFactor = (head.id_num % 1000) / 10000;
            const finalScore = Math.min(score + uniqueFactor, 100);

            let category = 'D';
            let badge = 'success';
            if (finalScore >= catAMin) { category = 'A'; badge = 'danger'; }
            else if (finalScore >= catBMin) { category = 'B'; badge = 'warning'; }
            else if (finalScore >= catCMin) { category = 'C'; badge = 'info'; }

            stats[category]++;
            stats.total++;

            const deliveryInfo = historyMap.get(head.id);
            const times = deliveryInfo ? deliveryInfo.times : 0;
            const totalQty = deliveryInfo ? parseFloat(deliveryInfo.total_qty) : 0;
            const lastDelivery = deliveryInfo ? deliveryInfo.last_delivery : 'لم يستلم أبداً';

            results.push({
                id_num: head.id_num,
                full_name: `${head.first_name} ${head.father_name || ''} ${head.grandfather_name || ''} ${head.family_name}`.replace(/\s+/g, ' ').trim(),
                members_count: familyMembers.length + 1,
                score: Math.round(finalScore * 100) / 100,
                category,
                badge,
                reasons: reasons.length > 0 ? reasons.join(' • ') : 'حالة مستقرة (المعيل سليم، السكن غير مدمر، لا يوجد مرضى أو أطفال دون 5 سنوات)',
                address: head.area_name || 'غير محدد',
                times_served: times,
                total_qty: totalQty,
                last_delivery: lastDelivery
            });
        }

        results.sort((a, b) => b.score - a.score);

        return { success: true, families: results, stats };
    } catch (err) {
        return { success: false, error: err.message };
    } finally {
        if (conn) await conn.end();
    }
});

// 4. Excel Export IPC Handler
ipcMain.handle('export-excel', async (event, { data, fileName }) => {
    try {
        const { filePath } = await dialog.showSaveDialog({
            title: 'حفظ ملف الإكسل',
            defaultPath: path.join(app.getPath('downloads'), fileName),
            filters: [
                { name: 'Excel Files', extensions: ['xlsx'] }
            ]
        });

        if (!filePath) return { cancelled: true };

        const worksheet = xlsx.utils.json_to_sheet(data);
        const workbook = xlsx.utils.book_new();
        xlsx.utils.book_append_sheet(workbook, worksheet, 'Audited Report');

        xlsx.writeFile(workbook, filePath);
        return { success: true, filePath };
    } catch (err) {
        return { success: false, error: err.message };
    }
});
