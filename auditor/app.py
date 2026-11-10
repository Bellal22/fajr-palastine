import streamlit as st
import pandas as pd
import mysql.connector
import plotly.express as px
import numpy as np
from datetime import datetime

# --- Configuration & Styling ---
st.set_page_config(page_title="Family Auditor & Distribution Analyzer", layout="wide")

st.markdown("""
    <style>
    .main {
        background-color: #f8f9fa;
    }
    .stMetric {
        background-color: #ffffff;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .critical-card {
        padding: 20px;
        border-radius: 10px;
        border-left: 5px solid #dc3545;
        background-color: #fff;
        margin-bottom: 10px;
    }
    </style>
""", unsafe_allow_html=True)

# --- Database Connection ---
@st.cache_resource
def get_connection():
    try:
        return mysql.connector.connect(
            host="127.0.0.1",
            user="u946172581_fajeryouth",
            password="REG2791999reg!",
            database="u946172581_fajeryouth"
        )
    except Exception as e:
        st.error(f"Error connecting to database: {e}")
        return None

def load_data():
    conn = get_connection()
    if not conn: return None, None
    
    # Load Persons
    persons_query = "SELECT * FROM persons"
    df_persons = pd.read_sql(persons_query, conn)
    
    # Load Distributions (Project Beneficiaries)
    dist_query = """
    SELECT pb.*, p.name as project_name 
    FROM project_beneficiaries pb
    JOIN projects p ON pb.project_id = p.id
    WHERE pb.status = 'مستلم'
    """
    df_dist = pd.read_sql(dist_query, conn)
    
    return df_persons, df_dist

# --- Analysis Logic ---
def evaluate_families(df_persons):
    # Separate Heads and Dependents
    heads = df_persons[df_persons['relative_id'].isna()].copy()
    dependents = df_persons[df_persons['relative_id'].notna()].copy()
    
    # Define Critical Keywords (Arabic)
    critical_keywords = ['سرطان', 'كانسر', 'كنسر', 'كيماوي', 'ورم', 'أورام', 'مصاب', 'إصابة', 'بتر', 'شلل', 'إعاقة', 'فشل كلوي']
    
    results = []
    
    for _, head in heads.iterrows():
        family_members = dependents[dependents['relative_id'] == head['id_num']]
        
        # 1. Health Status of Head (Father usually)
        head_condition = str(head['condition_description'] or "") + " " + str(head['family_name'] or "")
        is_head_critical = any(kw in head_condition for kw in critical_keywords)
        
        # 2. Health Status of Spouse (Mother usually)
        spouse = family_members[family_members['relationship'].isin(['زوجة', 'wife'])]
        is_spouse_critical = False
        if not spouse.empty:
            spouse_condition = str(spouse.iloc[0]['condition_description'] or "") + " " + str(spouse.iloc[0]['family_name'] or "")
            is_spouse_critical = any(kw in spouse_condition for kw in critical_keywords)
        
        # 3. Children Count
        children = family_members[~family_members['relationship'].isin(['زوجة', 'wife', 'أب', 'أم'])]
        children_count = len(children)
        
        # 4. Employment
        is_unemployed = head['employment_status'] in ['عاطل عن العمل', 'لا يعمل', 'unemployed']
        
        # --- SCORING SYSTEM ---
        score = 0
        reasons = []
        
        if is_head_critical: 
            score += 40
            reasons.append("الأب (أو المعيل) مصاب/مريض")
        
        if is_spouse_critical: 
            score += 30
            reasons.append("الأم مصابة/مريضة")
            
        if is_head_critical and is_spouse_critical:
            score += 30 # Bonus for both parents being critical
            reasons.append("‼️ الوالدان كلاهما في حالة حرجة")
            
        if is_unemployed: 
            score += 20
            reasons.append("لا يوجد معيل (عاطل)")
        
        score += min(children_count * 5, 25) 
        if children_count > 0:
            reasons.append(f"عدد الأطفال: {children_count}")
            
        # Classification Logic
        category = "عادية"
        if (is_head_critical and is_spouse_critical and children_count > 0) or score >= 100:
            category = "حرجة جداً 🆘"
        elif score >= 70:
            category = "حرجة"
        elif score >= 40:
            category = "متوسطة"
        
        results.append({
            'head_id': head['id_num'],
            'head_name': f"{head['first_name']} {head['family_name']}",
            'score': score,
            'category': category,
            'reasons': " | ".join(reasons),
            'children_count': children_count,
            'employment_status': head['employment_status'],
            'address': f"{head['city']} - {head['neighborhood']}"
        })
        
    return pd.DataFrame(results)

def analyze_distributions(df_dist, df_families):
    # Group by person (head)
    dist_stats = df_dist.groupby('person_id').agg({
        'id': 'count',
        'quantity': 'sum'
    }).rename(columns={'id': 'times_received', 'quantity': 'total_quantity'})
    
    # Merge with families (using id mapping if necessary, but here we'll assume person_id is linked to persons.id)
    # Actually, we need to map distribution person_id back to head_id if distributions are recorded for dependents too
    # For now, let's assume distributions are primarily to heads or we aggregate by family
    
    # Let's just calculate outliers on the raw distribution stats
    if dist_stats.empty:
        return pd.DataFrame(), [], []

    mean_qty = dist_stats['total_quantity'].mean()
    std_qty = dist_stats['total_quantity'].std()
    
    # Z-score Outliers
    dist_stats['z_score'] = (dist_stats['total_quantity'] - mean_qty) / (std_qty if std_qty > 0 else 1)
    
    over_served = dist_stats[dist_stats['z_score'] > 2]
    under_served = dist_stats[dist_stats['z_score'] < -1] # Families receiving significantly less than average
    
    return dist_stats, over_served, under_served

# --- Main UI ---
def main():
    st.title("🛡️ Family Auditor & Distribution Intelligence")
    st.sidebar.header("Settings & Thresholds")
    
    with st.spinner("Loading and analyzing data..."):
        df_persons, df_dist = load_data()
        if df_persons is None: return
        
        df_scored = evaluate_families(df_persons)
        dist_stats, over_served, under_served = analyze_distributions(df_dist, df_scored)
        
    # --- Dashboard Summary ---
    col1, col2, col3, col4 = st.columns(4)
    col1.metric("Total Families", len(df_scored))
    col2.metric("Very Critical", len(df_scored[df_scored['category'] == "حرجة جداً"]))
    col3.metric("Critical", len(df_scored[df_scored['category'] == "حرجة"]))
    col4.metric("Avg Score", round(df_scored['score'].mean(), 1))
    
    tabs = st.tabs(["📊 Evaluation Analysis", "📉 Distribution Gaps", "🔍 Individual Search"])
    
    with tabs[0]:
        st.subheader("Family Vulnerability Distribution")
        fig = px.pie(df_scored, names='category', color='category',
                     color_discrete_map={"حرجة جداً": "#721c24", "حرجة": "#f8d7da", "متوسطة": "#fff3cd", "عادية": "#d4edda"})
        st.plotly_chart(fig, use_container_width=True)
        
        st.subheader("Most Critical Cases")
        critical_filter = st.multiselect("Filter by Category", ["حرجة جداً", "حرجة", "متوسطة"], default=["حرجة جداً"])
        filtered_df = df_scored[df_scored['category'].isin(critical_filter)].sort_values('score', ascending=False)
        st.dataframe(filtered_df, use_container_width=True)
        
    with tabs[1]:
        st.subheader("Distribution Anomalies")
        col_a, col_b = st.columns(2)
        
        with col_a:
            st.warning("Over-Served Families (Potential Outliers)")
            st.write("These families received significantly more than the average population.")
            st.dataframe(over_served)
            
        with col_b:
            st.info("Under-Served Families")
            st.write("These families are categorized as 'Critical' but received less than average aid.")
            # Cross reference under_served with critical status
            # First map dist_stats back to persons to get names if possible
            st.dataframe(under_served)
            
        st.subheader("Families with Zero Aid (last 6 months)")
        # Identify heads who are not in dist_stats
        # Note: mapping persons.id to dist_stats index
        zero_aid_heads = df_persons[(df_persons['relative_id'].isna()) & (~df_persons['id'].isin(dist_stats.index))]
        st.error(f"Found {len(zero_aid_heads)} families who never received aid.")
        st.dataframe(zero_aid_heads[['id_num', 'first_name', 'family_name', 'phone', 'current_city']])

    with tabs[2]:
        st.subheader("Find Family Details")
        search_id = st.text_input("Enter ID Number (رقم الهوية)")
        if search_id:
            person = df_persons[df_persons['id_num'] == search_id]
            if not person.empty:
                st.write(person.iloc[0])
                # Show family members
                mems = df_persons[df_persons['relative_id'] == search_id]
                st.write("Family Members:", mems)
            else:
                st.error("Not found")

if __name__ == "__main__":
    main()
