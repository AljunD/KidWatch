import React, { useEffect, useState } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  SafeAreaView,
  StatusBar,
  FlatList,
  ActivityIndicator,
  Alert,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { apiRequest, ENDPOINTS } from "./api";
import dayjs from "dayjs";

const HistoryItem = ({ weekId, weekNumber, dateRange, status, isCurrent, onPress }: any) => {
  return (
    <TouchableOpacity
      style={[
        styles.historyCard,
        isCurrent && { borderColor: "#4A90E2", borderWidth: 2 }
      ]}
      onPress={onPress}
    >
      <View style={[styles.dateCircle, isCurrent && { backgroundColor: "#4A90E2" }]}>
        <Ionicons 
          name={isCurrent ? "calendar" : "checkmark-circle"} 
          size={24} 
          color={isCurrent ? "#fff" : "#4ECDC4"} 
        />
      </View>
      <View style={styles.historyInfo}>
        <Text style={[styles.weekTitle, isCurrent && { color: "#4A90E2" }]}>
          Week {weekNumber}
        </Text>
        <Text style={styles.dateRangeText}>{dateRange}</Text>
      </View>
      <View
        style={[
          styles.statusBadge,
          isCurrent && { backgroundColor: "#4ECDC4", borderRadius: 12, paddingHorizontal: 8 },
        ]}
      >
        <Text
          style={[
            styles.statusText,
            isCurrent && { color: "#fff", fontWeight: "900" },
          ]}
        >
          {status}
        </Text>
        <Ionicons
          name="chevron-forward"
          size={18}
          color={isCurrent ? "#fff" : "#94a3b8"}
        />
      </View>
    </TouchableOpacity>
  );
};

export default function ProgressHistoryScreen({ navigation, route }: any) {
  const { studentId } = route.params;
  const [historyData, setHistoryData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchHistory = async () => {
      setLoading(true);
      try {
        const res = await apiRequest<any>(
          ENDPOINTS.studentProgressHistory(studentId),
          "GET"
        );

        if (res.success && Array.isArray(res.data)) {
          const formatted = res.data.map((week: any) => ({
            weekId: week.week_id.toString(),
            weekNumber: week.week_number ?? "Unknown",
            dateRange:
              week.start_date && week.end_date
                ? `${dayjs(week.start_date).format("MMM D")} - ${dayjs(week.end_date).format("MMM D, YYYY")}`
                : "No dates available",
            status: week.status,
            isCurrent: week.status === "Current Week",
          }));
          const currentWeekIndex = formatted.findIndex((item) => item.isCurrent);
          if (currentWeekIndex > -1) {
            const currentWeekItem = formatted.splice(currentWeekIndex, 1)[0];
            formatted.unshift(currentWeekItem);
          }

          setHistoryData(formatted);
        } else {
          setHistoryData([]);
          Alert.alert("Notice", res.message || "No progress history available.");
        }
      } catch (err: any) {
        Alert.alert("Error", err.message || "Unable to fetch progress history.");
        setHistoryData([]);
      } finally {
        setLoading(false);
      }
    };

    fetchHistory();
  }, [studentId]);

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="dark-content" />
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => navigation.goBack()}>
          <Ionicons name="arrow-back" size={24} color="#1A365D" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Progress History</Text>
        <TouchableOpacity style={styles.filterButton} onPress={() => Alert.alert("Filter", "Filter options coming soon!")}>
          <Ionicons name="filter-outline" size={24} color="#4A90E2" />
        </TouchableOpacity>
      </View>

      <View style={styles.content}>
        <Text style={styles.sectionTitle}>Weekly Progress</Text>
        <Text style={styles.subtitle}>Select a week to view the full report</Text>

        {loading ? (
          <ActivityIndicator size="large" color="#4A90E2" />
        ) : historyData.length === 0 ? (
          <Text style={{ textAlign: "center", marginTop: 20, color: "#94a3b8", fontSize: 16 }}>
            No progress history available yet.
          </Text>
        ) : (
          <FlatList
            data={historyData}
            keyExtractor={(item) => item.weekId}
            showsVerticalScrollIndicator={false}
            renderItem={({ item }) => (
              <HistoryItem
                weekId={item.weekId}
                weekNumber={item.weekNumber}
                dateRange={item.dateRange}
                status={item.status}
                isCurrent={item.isCurrent}
                onPress={() =>
                  navigation.navigate("ProgressDetail", {
                    studentId,
                    weekId: item.weekId,
                    weekName: `Week ${item.weekNumber}`,
                  })
                }
              />
            )}
          />
        )}
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: "#F0F9FF" },
  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', padding: 20 },
  backButton: { backgroundColor: '#fff', padding: 10, borderRadius: 15, elevation: 2 },
  filterButton: { backgroundColor: '#fff', padding: 10, borderRadius: 15, elevation: 2 },
  headerTitle: { fontSize: 20, fontWeight: "900", color: "#1A365D" },
  content: { flex: 1, paddingHorizontal: 20 },
  sectionTitle: { fontSize: 24, fontWeight: "900", color: "#4A90E2", marginTop: 10 },
  subtitle: { fontSize: 14, color: "#64748b", marginBottom: 20, fontWeight: "500" },
  historyCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', borderRadius: 25, padding: 15, marginBottom: 15, elevation: 3, borderWidth: 2, borderColor: '#fff' },
  dateCircle: { width: 50, height: 50, borderRadius: 15, backgroundColor: '#4ECDC4', justifyContent: 'center', alignItems: 'center' },
  historyInfo: { flex: 1, marginLeft: 15 },
  weekTitle: { fontSize: 18, fontWeight: "800", color: "#1E293B" },
  dateRangeText: { fontSize: 13, color: "#64748b", fontWeight: "600", marginTop: 2 },
  statusBadge: { flexDirection: 'row', alignItems: 'center' },
  statusText: { fontSize: 12, fontWeight: "800", color: "#4ECDC4", marginRight: 5 },
});
