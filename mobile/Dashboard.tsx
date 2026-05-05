import React, { useContext, useEffect, useState } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  SafeAreaView,
  Image,
  StatusBar,
  ScrollView,
  ActivityIndicator,
  Alert,
} from "react-native";
import { Ionicons, MaterialCommunityIcons } from "@expo/vector-icons";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { AuthContext } from "./App";
import { apiRequest, ENDPOINTS } from "./api";
import { useFocusEffect } from "@react-navigation/native";

const MenuButton = ({ title, description, icon, color, onPress }: any) => (
  <TouchableOpacity
    style={[styles.menuItem, { borderColor: color + "30" }]}
    activeOpacity={0.6}
    onPress={onPress}
  >
    <View style={[styles.iconCircle, { backgroundColor: color }]}>
      <Ionicons name={icon} size={26} color="#fff" />
    </View>

    <View style={styles.textContainer}>
      <Text style={styles.buttonTitle}>{title}</Text>
      <Text style={styles.buttonDescription}>{description}</Text>
    </View>

    <View style={[styles.arrowCircle, { backgroundColor: color + "10" }]}>
      <Ionicons name="chevron-forward" size={18} color={color} />
    </View>
  </TouchableOpacity>
);

export default function DashboardScreen({ navigation, route }: any) {
  const { setIsAuthenticated } = useContext(AuthContext);

  const [loading, setLoading] = useState(true);
  const [guardian, setGuardian] = useState<any>(null);
  const [students, setStudents] = useState<any[]>([]);
  const [student, setStudent] = useState<any>(null);

  const handleLogout = async () => {
    await AsyncStorage.removeItem("token");
    setIsAuthenticated(false);
  };

  useEffect(() => {
    const fetchData = async () => {
      try {
        const token = await AsyncStorage.getItem("token");
        if (!token) {
          setIsAuthenticated(false);
          return;
        }

        // ✅ Fetch guardian profile (includes linked students)
        const profileRes = await apiRequest<any>(ENDPOINTS.profile, "GET");
        if (profileRes.success && profileRes.data) {
          setGuardian(profileRes.data);
          setStudents(profileRes.data.students || []);

          // ✅ Default student selection
          setStudent((prev: any) => prev || (profileRes.data.students?.[0] || null));
        } else {
          Alert.alert("Error", profileRes.message || "Unable to fetch profile.");
        }
      } catch (err) {
        console.error("Error fetching dashboard data:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  // ✅ Update student when coming back from SelectStudentScreen
  useFocusEffect(
    React.useCallback(() => {
      if (route.params?.selectedStudent) {
        setStudent(route.params.selectedStudent);
      }
    }, [route.params])
  );

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

      <ScrollView
        style={styles.container}
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {/* --- HEADER --- */}
        <View style={styles.welcomeSection}>
          <Text style={styles.welcomeText}>
            Hi, {guardian?.first_name || "Parent"} 👋
          </Text>
          <Text style={styles.subtitle}>Let's see how the day is going!</Text>
        </View>

        {/* --- STUDENT PROFILE CARD --- */}
        {student && (
          <View style={styles.card}>
            <View style={styles.cardContent}>
              {student.photo_path ? (
                <Image
                  source={{ uri: student.photo_path }}
                  style={styles.profileImage}
                />
              ) : (
                <View
                  style={[
                    styles.profileImage,
                    { backgroundColor: "#e5e7eb", justifyContent: "center", alignItems: "center" },
                  ]}
                >
                  <Ionicons name="person" size={40} color="#9ca3af" />
                </View>
              )}
              <View style={styles.nameContainer}>
                <Text style={styles.childLabel}>Current Student</Text>
                <Text style={styles.childName}>
                  {student.first_name} {student.last_name}
                </Text>
              </View>
            </View>
          </View>
        )}

        {/* --- MAIN NAVIGATION MENU --- */}
        <Text style={styles.sectionLabel}>MAIN MENU</Text>

        <MenuButton
          title="Weekly Progress"
          description="See all the great learning!"
          icon="stats-chart"
          color="#FF6B6B"
          onPress={() => navigation.navigate("WeeklyProgress", { student })}
        />

        <MenuButton
          title="Progress History"
          description="Look back at the fun times"
          icon="book"
          color="#4ECDC4"
          onPress={() =>
            navigation.navigate("ProgressHistory", { studentId: student.id })
          }
        />

        <MenuButton
          title="Account Profile"
          description="View guardian + students"
          icon="happy"
          color="#FFBE0B"
          onPress={() =>
            navigation.navigate("Profile", {
              studentId: student.id,
              guardianId: guardian.id,
            })
          }
        />

        {/* --- FOOTER ACTIONS (Switch & Logout) --- */}
        <View style={styles.footerActions}>
          <TouchableOpacity
            style={styles.switchButton}
            onPress={() =>
              navigation.navigate("SelectStudent", { currentStudent: student, fromDashboard: true })
            }
          >
            <Ionicons name="people-outline" size={20} color="#4A90E2" />
            <Text style={styles.switchText}>Switch Student</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
            <Text style={styles.logoutText}>Sign Out</Text>
            <MaterialCommunityIcons name="exit-run" size={20} color="#FF6B6B" />
          </TouchableOpacity>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}


const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: "#F0F9FF" },
  container: { flex: 1 },
  scrollContent: { paddingHorizontal: 20, paddingTop: 40, paddingBottom: 60 },
  welcomeSection: { marginBottom: 25 },
  welcomeText: { fontSize: 32, fontWeight: "900", color: "#1A365D" },
  subtitle: { fontSize: 16, color: "#64748b", fontWeight: "500" },
  sectionLabel: {
    fontSize: 13,
    fontWeight: "900",
    color: "#94a3b8",
    marginBottom: 15,
    letterSpacing: 2,
  },
  card: {
    backgroundColor: "#4A90E2",
    borderRadius: 35,
    padding: 24,
    elevation: 8,
    marginBottom: 35,
    borderWidth: 4,
    borderColor: "#fff",
  },
  cardContent: { flexDirection: "row", alignItems: "center" },
  profileImage: {
    width: 85,
    height: 85,
    borderRadius: 30,
    borderWidth: 3,
    borderColor: "#fff",
  },
  nameContainer: { marginLeft: 18, flex: 1 },
  childLabel: {
    color: "rgba(255,255,255,0.85)",
    fontSize: 10,
    fontWeight: "800",
    textTransform: "uppercase",
  },
  childName: { fontSize: 18, fontWeight: "900", color: "#fff", lineHeight: 26 },
  menuItem: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#fff",
    borderRadius: 25,
    padding: 16,
    marginBottom: 16,
    borderWidth: 2,
    elevation: 2,
  },
  iconCircle: {
    width: 50,
    height: 50,
    borderRadius: 18,
    justifyContent: "center",
    alignItems: "center",
  },
  textContainer: { flex: 1, marginLeft: 15 },
  buttonTitle: { fontSize: 17, fontWeight: "800", color: "#1e293b" },
  buttonDescription: { fontSize: 13, color: "#64748b" },
  arrowCircle: { padding: 6, borderRadius: 12 },
  footerActions: { marginTop: 20, alignItems: "center" },
  switchButton: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#fff",
    paddingVertical: 12,
    paddingHorizontal: 20,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: "#4A90E230",
    marginBottom: 15,
    width: "100%",
    justifyContent: "center",
  },
  switchText: {
    color: "#4A90E2",
    fontWeight: "800",
    fontSize: 15,
    marginLeft: 10,
  },
  logoutButton: {
    flexDirection: "row",
    alignItems: "center",
    padding: 10,
  },
  logoutText: {
    color: "#FF6B6B",
    fontWeight: "900",
    fontSize: 16,
    marginRight: 8,
  },
});
