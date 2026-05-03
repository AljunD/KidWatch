import React from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  SafeAreaView,
  Image,
  StatusBar,
  ScrollView,
} from "react-native";
import { Ionicons, MaterialCommunityIcons } from "@expo/vector-icons";

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
  // Receive the student data from SelectStudent.tsx
  const { studentName, studentImage } = route.params || { 
    studentName: 'Francisco, Christoper Jul', 
    studentImage: require("./assets/cjpic.jpg") 
  };

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
          <Text style={styles.welcomeText}>Hi, Parent! 👋</Text>
          <Text style={styles.subtitle}>Let's see how the day is going!</Text>
        </View>

        {/* --- STUDENT PROFILE CARD --- */}
        <View style={styles.card}>
          <View style={styles.cardContent}>
            <Image 
              source={studentImage} 
              style={styles.profileImage} 
            />
            <View style={styles.nameContainer}>
              <Text style={styles.childLabel}>Current Student</Text>
              <Text style={styles.childName}>{studentName}</Text>
            </View>
          </View>
        </View>

        {/* --- MAIN NAVIGATION MENU --- */}
        <Text style={styles.sectionLabel}>MAIN MENU</Text>
        
        <MenuButton 
          title="Weekly Progress" 
          description="See all the great learning!"
          icon="stats-chart" 
          color="#FF6B6B" 
          onPress={() => navigation.navigate("WeeklyProgress")} 
        />
        
        <MenuButton 
          title="Progress History" 
          description="Look back at the fun times"
          icon="book" 
          color="#4ECDC4" 
          onPress={() => navigation.navigate("ProgressHistory")} 
        />
        
        <MenuButton 
          title="Account Profile" 
          description="View your settings"
          icon="happy" 
          color="#FFBE0B" 
          onPress={() => navigation.navigate("StudentProfile", { 
          studentName: studentName, 
          studentImage: studentImage 
        })} 
        />

        {/* --- FOOTER ACTIONS (Switch & Logout) --- */}
        <View style={styles.footerActions}>
          <TouchableOpacity 
            style={styles.switchButton} 
            onPress={() => navigation.navigate("SelectStudent")}
          >
            <Ionicons name="people-outline" size={20} color="#4A90E2" />
            <Text style={styles.switchText}>Switch Student</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={styles.logoutButton} 
            onPress={() => navigation.replace("Login")}
          >
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
  sectionLabel: { fontSize: 13, fontWeight: "900", color: "#94a3b8", marginBottom: 15, letterSpacing: 2 },

  card: { 
    backgroundColor: "#4A90E2", 
    borderRadius: 35, 
    padding: 24, 
    elevation: 8, 
    marginBottom: 35, 
    borderWidth: 4, 
    borderColor: "#fff" 
  },
  cardContent: { flexDirection: "row", alignItems: "center" },
  profileImage: { width: 85, height: 85, borderRadius: 30, borderWidth: 3, borderColor: "#fff" },
  nameContainer: { marginLeft: 18, flex: 1 },
  childLabel: { color: "rgba(255,255,255,0.85)", fontSize: 10, fontWeight: "800", textTransform: "uppercase" },
  childName: { fontSize: 18, fontWeight: "900", color: "#fff", lineHeight: 26 },

  menuItem: { 
    flexDirection: "row", 
    alignItems: "center", 
    backgroundColor: "#fff", 
    borderRadius: 25, 
    padding: 16, 
    marginBottom: 16, 
    borderWidth: 2, 
    elevation: 2 
  },
  iconCircle: { width: 50, height: 50, borderRadius: 18, justifyContent: "center", alignItems: "center" },
  textContainer: { flex: 1, marginLeft: 15 },
  buttonTitle: { fontSize: 17, fontWeight: "800", color: "#1e293b" },
  buttonDescription: { fontSize: 13, color: "#64748b" },
  arrowCircle: { padding: 6, borderRadius: 12 },

  // Footer Button Styles
  footerActions: {
    marginTop: 20,
    alignItems: 'center'
  },
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
    width: '100%',
    justifyContent: 'center'
  },
  switchText: {
    color: "#4A90E2",
    fontWeight: "800",
    fontSize: 15,
    marginLeft: 10
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
    marginRight: 8
  },
});