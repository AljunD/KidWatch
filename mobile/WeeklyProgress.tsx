import React, { useState, useEffect, useMemo } from "react";
import { 
  View, 
  Text, 
  StyleSheet, 
  TouchableOpacity, 
  SafeAreaView, 
  StatusBar, 
  ScrollView,
  ActivityIndicator,
  Alert 
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { apiRequest, ENDPOINTS } from "./api"; // centralized API client
import dayjs from "dayjs";

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

export default function WeeklyProgressScreen({ navigation, route }: any) {
  const student = route.params?.student;
  const [progressRecords, setProgressRecords] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchProgress = async () => {
      try {
        // ✅ Fetch all progress records for this student
        const res = await apiRequest<any>(
          ENDPOINTS.studentProgress(student.id),
          "GET"
        );

        if (res.success) {
          // Map DB records to UI format
          const records = res.data.map((rec: any) => ({
            id: rec.id,
            name: rec.subject,
            date: dayjs(rec.created_at).format("MMM D, YYYY"),
            score: rec.rating_label !== "No Classes" ? rec.rating_label : null,
            remarks: rec.remarks,
            week: rec.week,
          }));
          setProgressRecords(records);
        } else {
          Alert.alert("Error", res.message || "Failed to load progress records.");
        }
      } catch (err: any) {
        Alert.alert("Error", err.message || "Unable to connect to server.");
      } finally {
        setLoading(false);
      }
    };

    fetchProgress();
  }, [student]);

  // ✅ Logic: At least 4 subjects graded (rating_level > 0)
  const isComplete = useMemo(() => {
    return progressRecords.filter(s => s.score !== null).length >= 4;
  }, [progressRecords]);

  const handleGenerate = async () => {
    if (isComplete) {
      try {
        const weekId = progressRecords[0]?.week?.id;
        const res = await apiRequest<any>(
          ENDPOINTS.generateSummary(student.id, weekId),
          "POST"
        );
        if (res.success) {
          Alert.alert("Awesome! 🎉", "Weekly summary generated successfully.");
        } else {
          Alert.alert("Error", res.message || "Failed to generate summary.");
        }
      } catch (err: any) {
        Alert.alert("Error", err.message || "Unable to connect to server.");
      }
    }
  };

  if (loading) {
    return (
      <SafeAreaView style={styles.safeArea}>
        <ActivityIndicator size="large" color="#4A90E2" style={{ flex: 1 }} />
      </SafeAreaView>
    );
  }

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
        {progressRecords.length > 0 && (
          <View style={styles.weekInfo}>
            <Text style={styles.weekLabel}>
              Week {progressRecords[0].week?.week_number} Progress
            </Text>
            <Text style={styles.weekDates}>
              {dayjs(progressRecords[0].week?.start_date).format("MMM D")} -{" "}
              {dayjs(progressRecords[0].week?.end_date).format("MMM D, YYYY")}
            </Text>
          </View>
        )}

        {progressRecords.map((item) => (
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