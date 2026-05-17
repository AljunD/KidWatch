import React, { useEffect, useState } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  StatusBar,
  ScrollView,
  ActivityIndicator,
  Alert,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { SafeAreaView } from "react-native-safe-area-context";
import dayjs from "dayjs";

import { apiRequest, ENDPOINTS } from "./api";

const COLORS = {
  bg: "#F0F9FF",
  white: "#FFFFFF",
  primary: "#003366",
  emerald: "#059669",
  amber: "#f59e0b",
  red: "#dc2626",
  blue: "#2563eb",
  gray: "#6b7280",
};

const getRatingStyle = (level: number) => {
  switch (level) {
    case 0: return { bg: "#e5e7eb", text: "#6b7280" };
    case 1: return { bg: "#fee2e2", text: "#b91c1c" };
    case 2: return { bg: "#fef3c7", text: "#b45309" };
    case 3: return { bg: "#dbeafe", text: "#1e40af" };
    case 4: return { bg: "#d1fae5", text: "#065f46" };
    default: return { bg: "#f3f4f6", text: "#6b7280" };
  }
};

export default function ProgressDetailScreen({ navigation, route }: any) {
  const { studentId, weekId } = route.params;

  const [progressRecords, setProgressRecords] = useState<any[]>([]);
  const [summary, setSummary] = useState<any | null>(null);
  const [activities, setActivities] = useState<any>({});
  const [canGenerate, setCanGenerate] = useState(false);
  const [summaryGenerated, setSummaryGenerated] = useState(false);
  const [weekInfo, setWeekInfo] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, [studentId, weekId]);

  const fetchData = async () => {
    setLoading(true);
    try {
      const res = await apiRequest<any>(
        ENDPOINTS.studentSummary(studentId, weekId),
        "GET"
      );

      if (res.success && res.data) {
        setSummary(res.data.summary || null);
        setProgressRecords(res.data.progress_records || []);
        setCanGenerate(res.data.can_generate || false);
        setSummaryGenerated(res.data.summary_generated || false);
        setWeekInfo(res.data.week_info || null);

        if (res.data.summary?.activities_text) {
          try {
            setActivities(JSON.parse(res.data.summary.activities_text));
          } catch {
            setActivities({});
          }
        }
      }
    } catch (err: any) {
      Alert.alert("Error", err.message || "Failed to fetch data.");
    } finally {
      setLoading(false);
    }
  };

  const handleGenerateSummary = async () => {
    const res = await apiRequest<any>(
      ENDPOINTS.generateSummary(studentId, weekId),
      "POST"
    );

    if (res.success) {
      Alert.alert(
        "Success",
        summaryGenerated
          ? "Summary regenerated."
          : "Summary generated."
      );
      fetchData();
    } else {
      Alert.alert("Error", res.message || "Failed.");
    }
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="dark-content" />

      {/* HEADER */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Ionicons name="arrow-back" size={22} color={COLORS.primary} />
        </TouchableOpacity>

        <Text style={styles.headerTitle}>Progress Report</Text>

        <View style={styles.backBtn}>
          <Ionicons name="document-text-outline" size={22} color={COLORS.primary} />
        </View>
      </View>

      <ScrollView style={styles.container}>
        {/* WEEK CARD */}
        {weekInfo && (
          <View style={styles.weekCard}>
            <Text style={styles.weekTitle}>Week {weekInfo.week_number}</Text>
            <Text style={styles.weekDate}>
              {dayjs(weekInfo.start_date).format("MMM D")} -{" "}
              {dayjs(weekInfo.end_date).format("MMM D, YYYY")}
            </Text>
          </View>
        )}

        {/* SUBJECT CARDS */}
        <Text style={styles.sectionTitle}>Subjects</Text>

        {loading ? (
          <ActivityIndicator size="large" />
        ) : (
          <View style={styles.grid}>
            {progressRecords.map((item: any) => {
              const style = getRatingStyle(item.rating_level);

              return (
                <View key={item.id} style={styles.subjectCard}>
                  <View style={styles.subjectHeader}>
                    <Text style={styles.subjectTitle}>{item.subject}</Text>

                    <View style={[styles.badge, { backgroundColor: style.bg }]}>
                      <Text style={[styles.badgeText, { color: style.text }]}>
                        {item.rating_label}
                      </Text>
                    </View>
                  </View>

                  <Text style={styles.remarks}>
                    {item.remarks || "No remarks provided."}
                  </Text>
                </View>
              );
            })}
          </View>
        )}

        {/* GENERATE BUTTON */}
        {!summary && (
          <TouchableOpacity
            style={[
              styles.generateBtn,
              !canGenerate && { backgroundColor: "#cbd5f5" },
            ]}
            disabled={!canGenerate}
            onPress={handleGenerateSummary}
          >
            <Text style={styles.generateText}>
              {canGenerate
                ? summaryGenerated
                  ? "Regenerate Summary"
                  : "Generate Summary"
                : "Complete grading first"}
            </Text>
          </TouchableOpacity>
        )}

        {/* SUMMARY */}
        {summary && (
          <>
            <View style={styles.summaryCard}>
              <Text style={styles.summaryTitle}>📘 Weekly Summary</Text>
              <Text style={styles.summaryText}>
                {summary.summary_text}
              </Text>
            </View>

            {/* ACTIVITIES */}
            <Text style={styles.sectionTitle}>Suggested Activities</Text>

            {Object.entries(activities).map(([category, subjects]: any) =>
              Object.entries(subjects).map(([subject, data]: any) => (
                <View key={subject} style={styles.activityCard}>
                  <Text style={styles.activityTitle}>{subject}</Text>

                  {data.recommendation && (
                    <Text style={styles.activityText}>
                      Activity: {data.recommendation}
                    </Text>
                  )}

                  {data.narrative && (
                    <Text style={styles.narrative}>{data.narrative}</Text>
                  )}

                  {data.guardian_tip && (
                    <Text style={styles.guardian}>
                      Guardian Tip: {data.guardian_tip}
                    </Text>
                  )}

                  {data.student_tip && (
                    <Text style={styles.student}>
                      Student Tip: {data.student_tip}
                    </Text>
                  )}
                </View>
              ))
            )}
          </>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: COLORS.bg },

  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    padding: 20,
    alignItems: "center",
  },

  backBtn: {
    backgroundColor: COLORS.white,
    padding: 10,
    borderRadius: 12,
  },

  headerTitle: {
    fontSize: 20,
    fontWeight: "900",
    color: COLORS.primary,
  },

  container: { paddingHorizontal: 20 },

  weekCard: {
    backgroundColor: "#ecfdf5",
    borderRadius: 16,
    padding: 15,
    borderWidth: 2,
    borderColor: COLORS.emerald,
    marginBottom: 20,
  },

  weekTitle: {
    fontSize: 18,
    fontWeight: "900",
    color: COLORS.primary,
  },

  weekDate: {
    fontSize: 13,
    color: COLORS.gray,
  },

  sectionTitle: {
    fontSize: 20,
    fontWeight: "900",
    color: COLORS.primary,
    marginBottom: 10,
  },

  grid: {
    gap: 10,
  },

  subjectCard: {
    backgroundColor: COLORS.white,
    borderRadius: 14,
    padding: 15,
    borderWidth: 1,
    borderColor: "#e5e7eb",
  },

  subjectHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },

  subjectTitle: {
    fontWeight: "800",
    fontSize: 16,
    color: COLORS.primary,
  },

  badge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },

  badgeText: {
    fontSize: 12,
    fontWeight: "700",
  },

  remarks: {
    marginTop: 6,
    fontSize: 13,
    color: COLORS.gray,
  },

  summaryCard: {
    backgroundColor: COLORS.white,
    padding: 15,
    borderRadius: 16,
    marginTop: 20,
  },

  summaryTitle: {
    fontWeight: "900",
    marginBottom: 8,
    color: COLORS.primary,
  },

  summaryText: {
    color: "#334155",
    lineHeight: 20,
  },

  activityCard: {
    backgroundColor: COLORS.white,
    padding: 15,
    borderRadius: 14,
    marginBottom: 10,
  },

  activityTitle: {
    fontWeight: "800",
    marginBottom: 5,
    color: COLORS.primary,
  },

  activityText: {
    fontSize: 14,
    marginBottom: 5,
  },

  narrative: {
    fontSize: 13,
    color: COLORS.gray,
    marginBottom: 5,
  },

  guardian: {
    color: COLORS.blue,
    fontSize: 13,
  },

  student: {
    color: COLORS.emerald,
    fontSize: 13,
  },

  generateBtn: {
    marginTop: 15,
    backgroundColor: COLORS.emerald,
    padding: 12,
    borderRadius: 20,
    alignItems: "center",
  },

  generateText: {
    color: "#fff",
    fontWeight: "800",
  },
});