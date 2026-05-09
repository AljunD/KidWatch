import React, { useEffect, useState, useContext } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Image,
  FlatList,
  StatusBar,
  ActivityIndicator,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context"; // ✅ fixed import
import { Ionicons } from "@expo/vector-icons";
import { apiRequest, ENDPOINTS } from "./api";
import { AuthContext } from "./AuthContext";

export default function SelectStudentScreen({ navigation }: any) {
  const { setAuthenticated, setSelectedStudent, selectedStudent } =
    useContext(AuthContext);

  const [loading, setLoading] = useState(true);
  const [students, setStudents] = useState<any[]>([]);

  useEffect(() => {
    const fetchStudents = async () => {
      try {
        const res = await apiRequest<any>(ENDPOINTS.students, "GET");
        if (res.success && res.data) {
          setStudents(res.data);
        }
      } catch (err) {
        console.error("Error fetching students:", err);
        setAuthenticated(false); // fallback if token invalid
      } finally {
        setLoading(false);
      }
    };

    fetchStudents();
  }, []);

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

      {/* Header */}
      <View style={styles.headerContainer}>
        <View style={styles.headerTextContainer}>
          <Text style={styles.welcomeText}>Parent Portal</Text>
          <Text style={styles.title}>Who's learning today?</Text>
        </View>
      </View>

      <FlatList
        data={students}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.list}
        renderItem={({ item }) => {
          const isActive = selectedStudent && selectedStudent.id === item.id;
          return (
            <TouchableOpacity
              style={[styles.card, isActive && styles.activeCard]}
              onPress={() => {
                setSelectedStudent(item); // ✅ store in context
                navigation.replace("Dashboard"); // ✅ go to Dashboard
              }}
            >
              {item.photo_path ? (
                <Image source={{ uri: item.photo_path }} style={styles.avatar} />
              ) : (
                <View
                  style={[
                    styles.avatar,
                    {
                      backgroundColor: "#e5e7eb",
                      justifyContent: "center",
                      alignItems: "center",
                    },
                  ]}
                >
                  <Ionicons name="person" size={28} color="#9ca3af" />
                </View>
              )}
              <View style={styles.info}>
                <Text style={styles.nameText}>
                  {item.last_name}, {item.first_name}
                </Text>
                <Text style={styles.subText}>
                  {isActive ? "Currently Active" : "View Progress Records"}
                </Text>
              </View>
              <View style={styles.arrowContainer}>
                <Ionicons name="chevron-forward" size={20} color="#4A90E2" />
              </View>
            </TouchableOpacity>
          );
        }}
        ListEmptyComponent={
          <Text style={styles.emptyText}>
            No students linked to this guardian yet.
          </Text>
        }
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: "#F0F9FF" },
  headerContainer: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingHorizontal: 20,
    paddingVertical: 15,
  },
  headerTextContainer: { alignItems: "center" },
  welcomeText: {
    fontSize: 13,
    color: "#94a3b8",
    fontWeight: "900",
    letterSpacing: 1.5,
    textTransform: "uppercase",
  },
  title: { fontSize: 26, fontWeight: "900", color: "#1A365D", marginTop: 5 },
  list: { paddingHorizontal: 20 },
  card: {
    backgroundColor: "#fff",
    flexDirection: "row",
    alignItems: "center",
    padding: 18,
    borderRadius: 25,
    marginBottom: 15,
    borderWidth: 2,
    borderColor: "#e2e8f0",
    elevation: 3,
  },
  activeCard: {
    borderColor: "#4A90E2",
    backgroundColor: "#E6F0FA",
  },
  avatar: {
    width: 65,
    height: 65,
    borderRadius: 20,
    borderWidth: 2,
    borderColor: "#F0F9FF",
  },
  info: { flex: 1, marginLeft: 15 },
  nameText: { fontSize: 17, fontWeight: "900", color: "#1A365D" },
  subText: { fontSize: 13, color: "#64748B", marginTop: 2 },
  arrowContainer: { backgroundColor: "#F0F9FF", padding: 8, borderRadius: 12 },
  emptyText: {
    textAlign: "center",
    marginTop: 40,
    color: "#94a3b8",
    fontSize: 16,
    fontWeight: "600",
  },
});
