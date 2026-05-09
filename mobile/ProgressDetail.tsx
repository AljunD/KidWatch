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
import { SafeAreaView } from "react-native-safe-area-context"; // ✅ fixed import
import dayjs from "dayjs";

import { apiRequest, ENDPOINTS } from "./api";

const COLORS = {
  bg: "#F0F9FF",
  white: "#FFFFFF",
  primary: "#1A365D",
  accent: "#4A90E2",
  teal: "#4ECDC4",
  amber: "#FFBE0B",
  rose: "#FF6B6B",
  textSecondary: "#64748b",
};

const RatingBadge = ({ rating }: { rating: string }) => {
  let bgColor = COLORS.textSecondary;

  switch (rating) {
    case "Needs Attention":
      bgColor = COLORS.rose;
      break;
    case "Excellent":
      bgColor = COLORS.teal;
      break;
    case "Very Good":
      bgColor = COLORS.accent;
      break;
    case "Good":
      bgColor = COLORS.amber;
      break;
    default:
      bgColor = COLORS.textSecondary;
      break;
  }

  return (
    <View style={[styles.badge, { backgroundColor: bgColor }]}>
      <Text style={styles.badgeText}>{rating?.toUpperCase()}</Text>
    </View>
  );
};

export default function ProgressDetailScreen({ navigation, route }: any) {
  const { studentId, weekId } = route.params;
  const [progressRecords, setProgressRecords] = useState<any[]>([]);
  const [summary, setSummary] = useState<any | null>(null);
  const [recommendations, setRecommendations] = useState<any[]>([]);
  const [canGenerate, setCanGenerate] = useState<boolean>(false);
  const [summaryGenerated, setSummaryGenerated] = useState<boolean>(false);
  const [weekInfo, setWeekInfo] = useState<any | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
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
              const parsed = JSON.parse(res.data.summary.activities_text);
              setRecommendations(Array.isArray(parsed) ? parsed : [parsed]);
            } catch {
              setRecommendations([]);
            }
          } else {
            setRecommendations([]);
          }
        } else {
          setSummary(null);
          setProgressRecords([]);
          setRecommendations([]);
          setCanGenerate(false);
          setSummaryGenerated(false);
          setWeekInfo(null);
        }
      } catch (err: any) {
        Alert.alert("Error", err.message || "Unable to fetch weekly progress.");
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [studentId, weekId]);

  const handleGenerateSummary = async () => {
    const res = await apiRequest<any>(
      ENDPOINTS.generateSummary(studentId, weekId),
      "POST"
    );
    if (res.success) {
      Alert.alert(
        "Success",
        summaryGenerated
          ? "Weekly summary regenerated successfully."
          : "Weekly summary generated successfully."
      );
      setSummary(res.data);
      setCanGenerate(false);
      setSummaryGenerated(true);
    } else {
      Alert.alert("Error", res.message || "Failed to generate summary.");
    }
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="dark-content" />
      <View style={styles.header}>
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Ionicons name="arrow-back" size={24} color={COLORS.primary} />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Progress Detail</Text>
        <View style={styles.filterButton}>
          <Ionicons
            name="document-text-outline"
            size={24}
            color={COLORS.accent}
          />
        </View>
      </View>

      <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
        {weekInfo && (
          <View style={styles.weekCard}>
            <Text style={styles.weekTitle}>Week {weekInfo.week_number}</Text>
            <Text style={styles.dateRangeText}>
              {weekInfo.start_date && weekInfo.end_date
                ? `${dayjs(weekInfo.start_date).format("MMM D")} - ${dayjs(
                    weekInfo.end_date
                  ).format("MMM D, YYYY")}`
                : "No dates available"}
            </Text>
          </View>
        )}

        <Text style={styles.sectionTitle}>Progress Records</Text>
        <Text style={styles.subtitle}>Ratings and remarks per subject</Text>

        {loading ? (
          <ActivityIndicator size="large" color={COLORS.accent} />
        ) : progressRecords.length === 0 ? (
          <Text style={styles.emptyText}>No progress records available.</Text>
        ) : (
          progressRecords.map((item: any) => (
            <View key={item.id} style={styles.recordCard}>
              <Text style={styles.subjectText}>
                {item.subject || "No subject"}
              </Text>
              <RatingBadge rating={item.rating_label || "No Rating"} />
              <Text style={styles.remarkText}>
                {item.remarks || "No remarks"}
              </Text>
            </View>
          ))
        )}

        <Text style={styles.sectionTitle}>Weekly Summary</Text>
        <Text style={styles.subtitle}>Analysis and recommendations</Text>

        {summary ? (
          <View style={styles.summaryCard}>
            {summary.summary_text && (
              <Text style={styles.summaryText}>{summary.summary_text}</Text>
            )}
            {recommendations.length > 0 ? (
              recommendations.map((rec, idx) => (
                <View key={idx} style={styles.recommendationCard}>
                  <Text style={styles.subjectText}>{rec.subject}</Text>
                  <Text style={styles.recommendationText}>{rec.activity}</Text>
                </View>
              ))
            ) : (
              <Text style={styles.emptyText}>No recommendations available.</Text>
            )}
          </View>
        ) : (
          <TouchableOpacity
            style={[
              styles.generateBtn,
              !canGenerate && { backgroundColor: "#94a3b8" },
            ]}
            onPress={handleGenerateSummary}
            disabled={!canGenerate}
          >
            <Text style={styles.generateText}>
              {canGenerate
                ? summaryGenerated
                  ? "Regenerate Weekly Summary"
                  : "Generate Weekly Summary"
                : "Complete grading to generate"}
            </Text>
          </TouchableOpacity>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: COLORS.bg },
  header: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    padding: 20,
  },
  backButton: {
    backgroundColor: COLORS.white,
    padding: 10,
    borderRadius: 15,
    elevation: 2,
  },
  filterButton: {
    backgroundColor: COLORS.white,
    padding: 10,
    borderRadius: 15,
    elevation: 2,
  },
  headerTitle: { fontSize: 20, fontWeight: "900", color: COLORS.primary },
  content: { flex: 1, paddingHorizontal: 20 },
  weekCard: {
    backgroundColor: COLORS.white,
    borderRadius: 20,
    padding: 15,
    marginBottom: 20,
    elevation: 2,
  },
  weekTitle: { fontSize: 18, fontWeight: "800", color: COLORS.accent },
  dateRangeText: {
    fontSize: 13,
    color: COLORS.textSecondary,
    fontWeight: "600",
    marginTop: 2,
  },
  sectionTitle: {
    fontSize: 22,
    fontWeight: "900",
    color: COLORS.accent,
    marginTop: 10,
  },
  subtitle: {
    fontSize: 14,
    color: COLORS.textSecondary,
    marginBottom: 15,
    fontWeight: "500",
  },
  recordCard: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: COLORS.white,
    borderRadius: 20,
    padding: 15,
    marginBottom: 10,
    elevation: 2,
  },
  subjectText: { 
    flex: 1, 
    fontSize: 16, 
    fontWeight: "700", 
    color: COLORS.primary },
  badge: { 
    paddingHorizontal: 10, 
    paddingVertical: 4, 
    borderRadius: 8, 
    marginRight: 10, 
    alignItems: "center", 
    justifyContent: "center" 
  },
  badgeText: { 
    color: COLORS.white, 
    fontSize: 11, 
    fontWeight: "900", 
    textTransform: "uppercase" 
  },
  remarkText: { 
    flex: 1, 
    color: COLORS.textSecondary, 
    fontSize: 13, 
    textAlign: "right", 
    fontStyle: "italic" 
  },

  summaryCard: { 
    backgroundColor: COLORS.white, 
    borderRadius: 20, 
    padding: 15, 
    marginBottom: 20, 
    elevation: 2 
  },
  summaryText: { 
    fontSize: 15, 
    fontWeight: "600", 
    color: "#334155", 
    marginBottom: 10, 
    lineHeight: 22 
  },
  recommendationCard: { 
    backgroundColor: "#F9FAFB", 
    borderRadius: 15, 
    padding: 12, 
    marginBottom: 10 
  },
  recommendationText: { 
    fontSize: 14, 
    color: COLORS.textSecondary, 
    lineHeight: 20 
  },

  emptyText: { 
    textAlign: "center", 
    marginTop: 20, 
    color: "#94a3b8", 
    fontSize: 16, 
    fontWeight: "600" 
  },

  generateBtn: { 
    marginTop: 15, 
    backgroundColor: COLORS.teal, 
    paddingVertical: 12, 
    borderRadius: 20, 
    alignItems: "center", 
    elevation: 3, 
    shadowColor: COLORS.teal, 
    shadowOpacity: 0.2, 
    shadowRadius: 6 
  },
  generateText: { 
    color: COLORS.white, 
    fontSize: 15, 
    fontWeight: "800" 
  },
});
