import React from "react";
import { 
  View, 
  Text, 
  StyleSheet, 
  TouchableOpacity, 
  SafeAreaView, 
  StatusBar, 
  ScrollView 
} from "react-native";
import { Ionicons } from "@expo/vector-icons";

// --- 1. THEME COLORS (Matching your Dashboard) ---
const COLORS = {
  bg: "#F0F9FF",
  white: "#FFFFFF",
  primary: "#1A365D",    // Dark Navy
  accent: "#4A90E2",     // Sky Blue
  teal: "#4ECDC4",       // Greenish-Teal
  amber: "#FFBE0B",      // Yellow
  rose: "#FF6B6B",       // Red/Pink
  textSecondary: "#64748b"
};

// --- 2. REUSABLE COMPONENTS ---

// Rating Badge helper
const RatingBadge = ({ rating }: { rating: string }) => {
  const bgColor = 
    rating === 'Excellent' ? COLORS.teal : 
    rating === 'Good' ? COLORS.amber : COLORS.rose;

  return (
    <View style={[styles.badge, { backgroundColor: bgColor }]}>
      <Text style={styles.badgeText}>{rating.toUpperCase()}</Text>
    </View>
  );
};

// Activity Recommendation Card
const ActivityCard = ({ subject, rating, recommendation, guardianTip, studentTip, priority }: any) => (
  <View style={styles.activityCard}>
    <View style={styles.activityHeader}>
      <Text style={[styles.activitySubject, { color: rating === 'Excellent' ? COLORS.teal : COLORS.rose }]}>
        {subject}
      </Text>
      <View style={[styles.prioBadge, { backgroundColor: priority === 'High' ? COLORS.rose + '15' : COLORS.teal + '15' }]}>
        <Text style={[styles.prioText, { color: priority === 'High' ? COLORS.rose : COLORS.teal }]}>
          {priority} Priority
        </Text>
      </View>
    </View>
    
    <Text style={styles.recommendationText}>{recommendation}</Text>
    
    <View style={styles.tipRow}>
      <Ionicons name="people" size={14} color={COLORS.primary} />
      <Text style={styles.tipText}><Text style={styles.tipLabel}>Guardian Tip: </Text>{guardianTip}</Text>
    </View>
    <View style={styles.tipRow}>
      <Ionicons name="school" size={14} color={COLORS.accent} />
      <Text style={styles.tipText}><Text style={styles.tipLabel}>Student Tip: </Text>{studentTip}</Text>
    </View>
  </View>
);

// --- 3. MAIN SCREEN ---
export default function ProgressDetailScreen({ navigation, route }: any) {
  const { weekName } = route.params;

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="dark-content" />

      {/* --- HEADER (Clean & Minimal) --- */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <Ionicons name="chevron-back" size={24} color={COLORS.primary} />
        </TouchableOpacity>
        <View style={styles.headerTitleContainer}>
          <Text style={styles.headerSubtitle}>Progress Record</Text>
          <Text style={styles.headerTitle}>Cj Francisco</Text>
        </View>
        <View style={{ width: 45 }} />
      </View>

      <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.scrollPadding}>
        
        {/* --- PERFORMANCE TABLE CARD --- */}
        <View style={styles.mainCard}>
          <View style={styles.weekInfo}>
            <Text style={styles.weekText}>{weekName}</Text>
            <View style={styles.currentBadge}>
              <Text style={styles.currentText}>LATEST</Text>
            </View>
          </View>

          {[
            { s: 'Math', r: 'Excellent', rem: 'Consistent' },
            { s: 'Science', r: 'Poor', rem: 'Needs focus' },
            { s: 'English', r: 'Poor', rem: 'Needs focus' },
            { s: 'Filipino', r: 'Good', rem: 'Improving' }
          ].map((item, index, arr) => (
            <View key={item.s} style={[styles.tableRow, index === arr.length - 1 && { borderBottomWidth: 0 }]}>
              <Text style={styles.subjectText}>{item.s}</Text>
              <RatingBadge rating={item.r} />
              <Text style={styles.remarkText}>{item.rem}</Text>
            </View>
          ))}
        </View>

        {/* --- AI RECOMMENDATIONS SECTION --- */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Recommendations</Text>
          <Text style={styles.sectionDesc}>Personalized analysis for this week</Text>
        </View>

        <ActivityCard 
          subject="English" rating="Poor" priority="High"
          recommendation="Focus on reading aloud and comprehension drills."
          guardianTip="Correct pronunciation during reading sessions."
          studentTip="Read 15 mins daily to improve fluency."
        />

        <ActivityCard 
          subject="Science" rating="Poor" priority="High"
          recommendation="Review lab notes and basic biology concepts."
          guardianTip="Use visual aids like videos for experiments."
          studentTip="Summarize lab results in your own words."
        />

      </ScrollView>
    </SafeAreaView>
  );
}

// --- 4. STYLES (Organized for consistency) ---
const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: COLORS.bg },
  header: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    justifyContent: 'space-between', 
    paddingHorizontal: 20, 
    paddingVertical: 15,
    backgroundColor: COLORS.white
  },
  backBtn: { backgroundColor: COLORS.bg, padding: 8, borderRadius: 12 },
  headerTitleContainer: { alignItems: 'center' },
  headerSubtitle: { fontSize: 12, color: COLORS.textSecondary, fontWeight: "600", textTransform: 'uppercase' },
  headerTitle: { fontSize: 18, fontWeight: "900", color: COLORS.primary },
  scrollPadding: { padding: 20 },

  // Main Card Styles
  mainCard: { 
    backgroundColor: COLORS.white, 
    borderRadius: 30, 
    padding: 20, 
    marginBottom: 25, 
    elevation: 4, 
    shadowColor: "#000", 
    shadowOpacity: 0.05, 
    shadowRadius: 10 
  },
  weekInfo: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 },
  weekText: { fontSize: 18, fontWeight: "800", color: COLORS.accent },
  currentBadge: { backgroundColor: COLORS.teal, paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8 },
  currentText: { color: COLORS.white, fontSize: 10, fontWeight: "900" },
  
  tableRow: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    paddingVertical: 15, 
    borderBottomWidth: 1, 
    borderBottomColor: "#F0F9FF" 
  },
  subjectText: { flex: 1.2, fontSize: 16, fontWeight: "700", color: COLORS.primary },
  badge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8, marginRight: 10 },
  badgeText: { color: COLORS.white, fontSize: 10, fontWeight: "900" },
  remarkText: { flex: 1, color: COLORS.textSecondary, fontSize: 12, textAlign: 'right' },

  // Recommendation Section Styles
  sectionHeader: { marginBottom: 15 },
  sectionTitle: { fontSize: 22, fontWeight: "900", color: COLORS.primary },
  sectionDesc: { fontSize: 14, color: COLORS.textSecondary, fontWeight: "500" },
  
  activityCard: { 
    backgroundColor: COLORS.white, 
    borderRadius: 25, 
    padding: 20, 
    marginBottom: 15, 
    elevation: 2,
    borderWidth: 1,
    borderColor: "#E2E8F0"
  },
  activityHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 },
  activitySubject: { fontSize: 16, fontWeight: "800" },
  prioBadge: { paddingHorizontal: 8, paddingVertical: 4, borderRadius: 8 },
  prioText: { fontSize: 10, fontWeight: "800" },
  recommendationText: { fontSize: 15, fontWeight: "700", color: "#334155", marginBottom: 15, lineHeight: 22 },
  tipRow: { flexDirection: 'row', alignItems: 'center', marginTop: 8 },
  tipText: { fontSize: 13, color: COLORS.textSecondary, marginLeft: 8, flex: 1 },
  tipLabel: { fontWeight: "800", color: COLORS.primary },
});