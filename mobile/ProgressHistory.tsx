import React, { useState } from "react";
import { View, Text, StyleSheet, TouchableOpacity, SafeAreaView, StatusBar, FlatList } from "react-native";
import { Ionicons } from "@expo/vector-icons";

const HistoryItem = ({ week, dateRange, status, onPress }: any) => (
  <TouchableOpacity style={styles.historyCard} onPress={onPress}>
    <View style={styles.dateCircle}>
      <Ionicons name="calendar" size={24} color="#4ECDC4" />
    </View>
    <View style={styles.historyInfo}>
      <Text style={styles.weekTitle}>{week}</Text>
      <Text style={styles.dateRangeText}>{dateRange}</Text>
    </View>
    <View style={styles.statusBadge}>
      <Text style={styles.statusText}>{status}</Text>
      <Ionicons name="chevron-forward" size={18} color="#94a3b8" />
    </View>
  </TouchableOpacity>
);

export default function ProgressHistoryScreen({ navigation }: any) {
  const [historyData] = useState([
    { id: '1', week: 'Week 4', date: 'March 24 - 28, 2026', status: 'Completed' },
    { id: '2', week: 'Week 3', date: 'March 17 - 21, 2026', status: 'Completed' },
    { id: '3', week: 'Week 2', date: 'March 10 - 14, 2026', status: 'Completed' },
    { id: '4', week: 'Week 1', date: 'March 03 - 07, 2026', status: 'Completed' },
  ]);

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="dark-content" />
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => navigation.goBack()}>
          <Ionicons name="arrow-back" size={24} color="#1A365D" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Progress History</Text>
        <TouchableOpacity style={styles.filterButton}>
          <Ionicons name="filter-outline" size={24} color="#4A90E2" />
        </TouchableOpacity>
      </View>

      <View style={styles.content}>
        <Text style={styles.sectionTitle}>Past Summaries</Text>
        <Text style={styles.subtitle}>Select a week to view the full report</Text>

        <FlatList
          data={historyData}
          keyExtractor={(item) => item.id}
          showsVerticalScrollIndicator={false}
          renderItem={({ item }) => (
            <HistoryItem 
              week={item.week} 
              dateRange={item.date} 
              status={item.status}
              onPress={() => navigation.navigate("ProgressDetail", { weekName: item.week })}
            />
          )}
        />
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
  dateCircle: { width: 50, height: 50, borderRadius: 15, backgroundColor: '#4ECDC415', justifyContent: 'center', alignItems: 'center' },
  historyInfo: { flex: 1, marginLeft: 15 },
  weekTitle: { fontSize: 18, fontWeight: "800", color: "#1E293B" },
  dateRangeText: { fontSize: 13, color: "#64748b", fontWeight: "600", marginTop: 2 },
  statusBadge: { flexDirection: 'row', alignItems: 'center' },
  statusText: { fontSize: 12, fontWeight: "800", color: "#4ECDC4", marginRight: 5 },
});