import React, { useEffect, useState } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Image,
  ImageBackground,
  ScrollView,
  StatusBar,
  ActivityIndicator,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import dayjs from "dayjs";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { SafeAreaView } from "react-native-safe-area-context";

import { apiRequest, ENDPOINTS } from "./api";

const InfoRow = ({ label, value, icon, color }: any) => (
  <View style={styles.infoRow}>
    <View style={styles.labelContainer}>
      <View style={[styles.iconCircle, { backgroundColor: color + "15" }]}>
        <Ionicons name={icon} size={18} color={color} />
      </View>
      <Text style={styles.label}>{label}</Text>
    </View>
    <Text style={styles.value}>{value || "—"}</Text>
  </View>
);

export default function ProfileScreen({ navigation, route }: any) {
  const { studentId, guardianId } = route.params || {};
  const [guardian, setGuardian] = useState<any>(null);
  const [student, setStudent] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const token = await AsyncStorage.getItem("token");
        if (!token) return;

        const guardianRes = await apiRequest<any>(ENDPOINTS.profile, "GET");
        if (guardianRes.success && guardianRes.data) {
          setGuardian(guardianRes.data);
        }

        if (studentId) {
          const studentRes = await apiRequest<any>(
            ENDPOINTS.studentDetail(studentId),
            "GET"
          );
          if (studentRes.success && studentRes.data) {
            setStudent(studentRes.data);
          }
        }
      } catch (err) {
        console.error("Error fetching profile data:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [studentId, guardianId]);

  if (loading) {
    return (
      <SafeAreaView style={styles.safeArea}>
        <ActivityIndicator size="large" color="#4A90E2" style={{ flex: 1 }} />
      </SafeAreaView>
    );
  }

  if (!student || !guardian) {
    return (
      <SafeAreaView style={styles.safeArea}>
        <Text
          style={{
            textAlign: "center",
            marginTop: 50,
            color: "#FF6B6B",
            fontWeight: "700",
          }}
        >
          Missing profile data. Please go back to Dashboard.
        </Text>
      </SafeAreaView>
    );
  }

  const fullName = `${student.first_name} ${
    student.middle_name ? student.middle_name + " " : ""
  }${student.last_name}`;
  const guardianName = `${guardian.first_name} ${
    guardian.middle_name ? guardian.middle_name + " " : ""
  }${guardian.last_name}`;
  const age = student.date_of_birth
    ? dayjs().diff(dayjs(student.date_of_birth), "year")
    : null;

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="dark-content" />
      <View style={styles.header}>
        <TouchableOpacity
          onPress={() => navigation.goBack()}
          style={styles.backButton}
        >
          <Ionicons name="arrow-back" size={24} color="#1A365D" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Profile</Text>
        <View style={{ width: 40 }} />
      </View>

      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingBottom: 40 }}
      >
        <ImageBackground
          source={require("./assets/doodle.jpg")}
          style={styles.doodleBanner}
          imageStyle={{
            borderBottomLeftRadius: 40,
            borderBottomRightRadius: 40,
          }}
          resizeMode="cover"
        >
          <View style={styles.imageWrapper}>
            {student.photo_path ? (
              <Image
                source={{ uri: student.photo_path }}
                style={styles.profileImage}
              />
            ) : (
              <View
                style={[
                  styles.profileImage,
                  {
                    backgroundColor: "#e5e7eb",
                    justifyContent: "center",
                    alignItems: "center",
                  },
                ]}
              >
                <Ionicons name="person" size={40} color="#9ca3af" />
              </View>
            )}
          </View>
        </ImageBackground>

        <View style={styles.content}>
          <View style={styles.infoCard}>
            <View style={styles.cardHeaderContainer}>
              <View style={styles.headerIconCircle}>
                <Ionicons name="person" size={20} color="#fff" />
              </View>
              <Text style={styles.cardHeader}>Student Details</Text>
            </View>

            <InfoRow
              label="Full Name"
              value={fullName}
              icon="person-outline"
              color="#FF6B6B"
            />
            <InfoRow
              label="Age"
              value={age ? `${age} Years Old` : "—"}
              icon="calendar-outline"
              color="#4A90E2"
            />
            <InfoRow
              label="Gender"
              value={student.gender}
              icon="male-female-outline"
              color="#FFBE0B"
            />
            <InfoRow
              label="Birth Date"
              value={dayjs(student.date_of_birth).format("MMM D, YYYY")}
              icon="gift-outline"
              color="#4ECDC4"
            />
            <InfoRow
              label="Nationality"
              value={student.nationality}
              icon="flag-outline"
              color="#FF9F1C"
            />
            <InfoRow
              label="Religion"
              value={student.religion}
              icon="book-outline"
              color="#9b59b6"
            />
          </View>
          <View style={styles.infoCard}>
            <View style={styles.cardHeaderContainer}>
              <View
                style={[styles.headerIconCircle, { backgroundColor: "#4ECDC4" }]}
              >
                <Ionicons name="heart" size={20} color="#fff" />
              </View>
              <Text style={styles.cardHeader}>Guardian Details</Text>
            </View>

            <InfoRow
              label="Name"
              value={guardianName}
              icon="business-outline"
              color="#4A90E2"
            />
            <InfoRow
              label="Relationship"
              value={guardian.relationship_to_child}
              icon="people-outline"
              color="#FF6B6B"
            />
            <InfoRow
              label="Email"
              value={guardian.user?.email}
              icon="mail-outline"
              color="#4ECDC4"
            />
            <InfoRow
              label="Contact"
              value={guardian.contact_number}
              icon="call-outline"
              color="#FFBE0B"
            />
            <InfoRow
              label="Home Address"
              value={guardian.address}
              icon="home-outline"
              color="#FF9F1C"
            />
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: "#F0F9FF" },
  header: {
    height: 70,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    paddingHorizontal: 20,
    backgroundColor: "#F0F9FF",
  },
  headerTitle: { color: "#1A365D", fontSize: 20, fontWeight: "900" },
  backButton: {
    backgroundColor: "#fff",
    padding: 8,
    borderRadius: 15,
    elevation: 2,
  },
  doodleBanner: {
    height: 180,
    width: "100%",
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#2d8ac7",
    borderBottomLeftRadius: 40,
    borderBottomRightRadius: 40,
    overflow: "hidden",
  },
  imageWrapper: {
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.2,
    shadowRadius: 10,
    elevation: 12,
  },
  profileImage: {
    width: 110,
    height: 110,
    borderRadius: 35,
    borderWidth: 4,
    borderColor: "#fff",
  },
  content: { padding: 20, marginTop: 10 },
  infoCard: {
    backgroundColor: "#fff",
    borderRadius: 30,
    padding: 22,
    marginBottom: 20,
    elevation: 4,
    shadowColor: "#000",
    shadowOpacity: 0.05,
    shadowRadius: 10,
  },
  cardHeaderContainer: { flexDirection: "row", alignItems: "center", marginBottom: 20 },
  headerIconCircle: {
    width: 36,
    height: 36,
    borderRadius: 12,
    backgroundColor: "#FF6B6B",
    justifyContent: "center",
    alignItems: "center",
    marginRight: 12,
  },
  cardHeader: { fontSize: 18, fontWeight: "900", color: "#1A365D" },
  infoRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 16,
    alignItems: "center",
  },
  labelContainer: { flexDirection: "row", alignItems: "center" },
  iconCircle: {
    width: 32,
    height: 32,
    borderRadius: 10,
    justifyContent: "center",
    alignItems: "center",
    marginRight: 10,
  },
  label: {
    fontSize: 13,
    color: "#94a3b8",
    fontWeight: "700",
  },
  value: {
    fontSize: 14,
    color: "#1A365D",
    fontWeight: "800",
  },
});
