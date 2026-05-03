import React, { useState, useMemo } from "react";
import { 
  View, 
  Text, 
  StyleSheet, 
  TouchableOpacity, 
  SafeAreaView, 
  StatusBar, 
  ScrollView,
  Alert 
} from "react-native";
import { Ionicons } from "@expo/vector-icons";

const SubjectCard = ({ subject, date, score, remarks, isPending }: any) => (
  <View style={[styles.card, isPending && styles.pendingCard]}>
    {/* Card Header */}
    <View style={[styles.cardHeader, isPending && { backgroundColor: '#94a3b8' }]}>
      <View style={styles.headerTitleRow}>
        <Ionicons 
          name={isPending ? "time" : "checkmark-circle"} 
          size={20} 
          color="#fff" 
          style={{ marginRight: 8 }} 
        />
        <Text style={styles.subjectText}>{subject}</Text>
      </View>
      <Text style={styles.dateText}>{date}</Text>
    </View>

    {/* Card Body */}
    <View style={styles.cardBody}>
      {isPending ? (
        <View style={styles.pendingInner}>
          <Text style={styles.pendingText}>Waiting for teacher's assessment...</Text>
        </View>
      ) : (
        <>
          <View style={styles.row}>
            <Text style={styles.label}>Score:</Text>
            <View style={styles.badge}>
              <Text style={styles.badgeText}>{score}</Text>
            </View>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Remarks:</Text>
            <Text style={styles.remarkText}>{remarks}</Text>
          </View>
        </>
      )}
    </View>
  </View>
);

export default function WeeklyProgressScreen({ navigation }: any) {
  // Mock Data: Toggle the 'score' to null to see the "Pending" state
  const [subjects] = useState([
    { id: 1, name: "Mathematics", date: "April 1, 2026", score: "Excellent", remarks: "Great with numbers!" },
    { id: 2, name: "English", date: "April 2, 2026", score: "Excellent", remarks: "Reading is improving." },
    { id: 3, name: "Science", date: "April 3, 2026", score: "Excellent", remarks: "Loves the plant lesson." },
    { id: 4, name: "Arts", date: "April 4, 2026", score: null, remarks: null }, // PENDING SUBJECT
  ]);

  // Logic: Check if all 4 are completed
  const isComplete = useMemo(() => {
    return subjects.filter(s => s.score !== null).length >= 4;
  }, [subjects]);

  const handleGenerate = () => {
    if (isComplete) {
      Alert.alert("Awesome! 🎉", "Generating your weekly summary report...");
    }
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="dark-content" />
      
      {/* Cloud Header */}
      <View style={styles.headerContainer}>
        <TouchableOpacity style={styles.backButton} onPress={() => navigation.goBack()}>
          <Ionicons name="arrow-back" size={24} color="#1A365D" />
        </TouchableOpacity>
        <Text style={styles.navTitle}>Weekly Progress</Text>
        <View style={{ width: 45 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.weekInfo}>
          <Text style={styles.weekLabel}>Week 1 Progress</Text>
          <Text style={styles.weekDates}>April 1 - April 4, 2026</Text>
        </View>

        {subjects.map((item) => (
          <SubjectCard 
            key={item.id}
            subject={item.name} 
            date={item.date} 
            score={item.score} 
            remarks={item.remarks} 
            isPending={item.score === null}
          />
        ))}

        {/* Generate Summary Button */}
        <TouchableOpacity 
          style={[styles.summaryButton, !isComplete && styles.buttonDisabled]}
          onPress={handleGenerate}
          disabled={!isComplete}
          activeOpacity={0.8}
        >
          <Ionicons 
            name={isComplete ? "document-text" : "lock-closed"} 
            size={22} 
            color="#fff" 
            style={{ marginRight: 10 }} 
          />
          <Text style={styles.summaryButtonText}>
            {isComplete ? "Generate Summary" : "Complete 4 Subjects to Unlock"}
          </Text>
        </TouchableOpacity>
        
        {!isComplete && (
          <Text style={styles.lockHint}>
            Please wait for all subjects to be graded.
          </Text>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: "#F0F9FF" },
  headerContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingVertical: 15,
  },
  backButton: {
    backgroundColor: '#fff',
    padding: 10,
    borderRadius: 15,
    elevation: 2,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowRadius: 5,
  },
  navTitle: { fontSize: 20, fontWeight: "900", color: "#1A365D" },
  scrollContent: { padding: 20 },
  
  weekInfo: { marginBottom: 25, alignItems: 'center' },
  weekLabel: { fontSize: 26, fontWeight: "900", color: "#4A90E2" },
  weekDates: { fontSize: 14, color: "#64748b", fontWeight: "600" },

  // Card Styling
  card: { 
    width: "100%", 
    backgroundColor: "#fff", 
    borderRadius: 30, 
    marginBottom: 18, 
    elevation: 4, 
    shadowColor: "#000", 
    shadowOffset: { width: 0, height: 4 }, 
    shadowOpacity: 0.05, 
    shadowRadius: 10, 
    overflow: "hidden",
    borderWidth: 2,
    borderColor: '#fff'
  },
  pendingCard: { borderColor: '#e2e8f0', backgroundColor: '#f8fafc' },
  cardHeader: { 
    backgroundColor: "#4A90E2", 
    flexDirection: "row", 
    justifyContent: "space-between", 
    alignItems: "center", 
    paddingHorizontal: 20, 
    paddingVertical: 12 
  },
  headerTitleRow: { flexDirection: 'row', alignItems: 'center' },
  subjectText: { color: "#fff", fontSize: 18, fontWeight: "800" },
  dateText: { color: "rgba(255,255,255,0.8)", fontSize: 11, fontWeight: "700" },
  
  cardBody: { padding: 20 },
  row: { flexDirection: "row", alignItems: "center", marginBottom: 10 },
  label: { fontSize: 14, fontWeight: "800", color: "#94a3b8", width: 70 },
  badge: { backgroundColor: "#4ECDC4", paddingHorizontal: 12, paddingVertical: 4, borderRadius: 10 },
  badgeText: { color: "#fff", fontWeight: "900", fontSize: 12 },
  remarkText: { fontSize: 14, color: "#1A365D", fontWeight: "700", flex: 1 },
  
  pendingInner: { paddingVertical: 5 },
  pendingText: { color: "#94a3b8", fontSize: 14, fontStyle: 'italic', fontWeight: "600" },

  // Button Styling
  summaryButton: { 
    backgroundColor: "#FF6B6B", 
    flexDirection: 'row',
    width: "100%", 
    paddingVertical: 18, 
    borderRadius: 25, 
    alignItems: "center", 
    justifyContent: 'center',
    marginTop: 15,
    elevation: 5,
    shadowColor: "#FF6B6B",
    shadowOpacity: 0.3,
    shadowRadius: 10,
  },
  buttonDisabled: { backgroundColor: "#cbd5e1", shadowOpacity: 0 },
  summaryButtonText: { color: "#fff", fontSize: 16, fontWeight: "900" },
  lockHint: { textAlign: 'center', color: '#94a3b8', fontSize: 12, marginTop: 10, fontWeight: '600' }
});