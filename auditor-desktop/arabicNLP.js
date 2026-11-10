/**
 * Arabic NLP and Medical Condition Classifier
 * Provides smart normalization of Arabic characters and fuzzy/keyword scoring.
 */

// Normalize Arabic text to reduce variations in writing styles
function normalizeArabic(text) {
    if (!text) return '';

    let str = text.trim().toLowerCase();

    // Remove diacritics (Tashkeel)
    str = str.replace(/[\u064B-\u0652]/g, '');

    // Normalize Alef variants to bare Alef
    str = str.replace(/[أإآ]/g, 'ا');

    // Normalize Teh Marbuta to Heh
    str = str.replace(/ة/g, 'ه');

    // Normalize Ya/Alef Maqsura
    str = str.replace(/ى/g, 'ي');

    // Remove punctuation & extra whitespace
    str = str.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()?"'+-]/g, ' ');
    str = str.replace(/\s+/g, ' ');

    return str.trim();
}

// Define clinical tiers with keywords and synonyms
const medicalLibrary = {
    critical: {
        score: 80,
        label: 'حالة حرجة جداً / خطر على الحياة',
        keywords: [
            'سرطان', 'كانسر', 'سرطانيه', 'خبيث', 'اورام', 'ورم خبيث',
            'فشل كلوي', 'غسيل كلى', 'غسل كلى', 'يغسل كلى', 'قصور كلوي حاد',
            'قلب مفتوح', 'تغيير صمام', 'انسداد شرايين', 'شرايين القلب', 'ضعف عضلة القلب',
            'زراعه نخاع', 'نخاع عظمي',
            'فشل كبد', 'تليف كبد',
            'ورم دماغ', 'كتلة دماغيه', 'سرطان الدم', 'لوكيميا', 'نزيف دماغ', 'نزيف الدماغ'
        ]
    },
    serious: {
        score: 55,
        label: 'حالة خطيرة / عجز معيل',
        keywords: [
            'شلل', 'بتر', 'مبتور', 'جلطه دماغيه', 'جلطه قلبيه',
            'اصابه حرب', 'جريح', 'اصيب بقصف', 'طلق ناري',
            'بلاتين', 'كسر حوض', 'كسر العمود الفقري',
            'ضمور عضلات', 'ضمور النخاع', 'تصلب متعدد', 'ام اس'
        ]
    },
    moderate: {
        score: 30,
        label: 'حالة متوسطة / إعاقة أو مرض يعيق العمل',
        keywords: [
            'اعاقه', 'كفيف', 'اعمى', 'فقد بصر', 'صم', 'بكم', 'اعاقه حركيه', 'اعاقه ذهنيه',
            'غضروف', 'ديسك', 'انزلاق غضروفي',
            'صرع', 'تشنج', 'كهرباء في الدماغ',
            'توحد', 'متلازمه داون', 'داون',
            'ثقب في القلب', 'ثقب بالقلب',
            'قدم سكريه', 'روماتويد', 'ذئبه حمرا'
        ]
    },
    stable: {
        score: 10,
        label: 'حالة مستقرة / أمراض مزمنة شائعة',
        keywords: [
            'سكري', 'سكر', 'ضغط', 'ضغط الدم', 'جلوكوما', 'مياه بيضاء',
            'ربو', 'ازمه صدريه', 'حساسيه',
            'شيخوخة', 'كبر سن', 'مسن',
            'سوء تغذيه', 'ضعف نمو',
            'غده درقيه', 'خمول غده', 'فتق'
        ]
    }
};

/**
 * Calculates a medical score and reason label based on description text
 * @param {string} rawDescription 
 * @param {string} customKeywordsPipeSeparated - custom user-defined keywords
 * @returns {{ score: number, tier: string, label: string, match: string }}
 */
function calculateMedicalScore(rawDescription, customKeywordsPipeSeparated = '') {
    if (!rawDescription) {
        return { score: 0, tier: 'none', label: 'حالة مستقرة', match: '' };
    }

    const normalized = normalizeArabic(rawDescription);

    // Check custom keywords first
    if (customKeywordsPipeSeparated) {
        const customs = customKeywordsPipeSeparated.split('|')
            .map(w => normalizeArabic(w))
            .filter(w => w.length > 0);

        for (const word of customs) {
            if (normalized.includes(word)) {
                return {
                    score: 75, // Default custom keyword score
                    tier: 'custom',
                    label: `كلمة مخصصة: ${word}`,
                    match: word
                };
            }
        }
    }

    // Match against built-in library (from highest severity to lowest)
    for (const tier of ['critical', 'serious', 'moderate', 'stable']) {
        const config = medicalLibrary[tier];
        for (const keyword of config.keywords) {
            const normalizedKeyword = normalizeArabic(keyword);
            if (normalized.includes(normalizedKeyword)) {
                return {
                    score: config.score,
                    tier: tier,
                    label: config.label,
                    match: keyword
                };
            }
        }
    }

    return { score: 0, tier: 'none', label: 'حالة مستقرة', match: '' };
}

module.exports = {
    normalizeArabic,
    calculateMedicalScore,
    medicalLibrary
};
