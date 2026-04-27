import React from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  SafeAreaView,
  Image,
  StatusBar,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";

/**
 * Reusable Menu Item Component
 */
const MenuButton = ({ title, icon, onPress }: any) => (
  <TouchableOpacity 
    style={styles.inputContainer} 
    activeOpacity={0.7} 
    onPress={onPress}
  >
    <View style={styles.iconCircle}>
      <Ionicons name={icon} size={22} color="#2d8ac7" />
    </View>
    <Text style={styles.buttonText}>{title}</Text>
    <Ionicons name="chevron-forward" size={20} color="#fff" />
  </TouchableOpacity>
);

export default function DashboardScreen({ navigation }: any) {
  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="light-content" />

      {/* --- HEADER --- */}
      <View style={styles.header}>
        <Text style={styles.title}>KidWatch</Text>
      </View>

      <View style={styles.overlay}>
        {/* --- WELCOME SECTION --- */}
        <View style={styles.welcomeContainer}>
          <Text style={styles.welcomeText}>Welcome, Parent!</Text>
          <Text style={styles.subtitle}>Here is your child's overview</Text>
        </View>

        {/* --- STUDENT PROFILE CARD --- */}
        <View style={styles.card}>
          <Image 
            source={require("./assets/cjpic.jpg")} 
            style={styles.profileImage} 
          />
          <View style={styles.infoContainer}>
            <Text style={styles.childName}>Francisco, Christoper</Text>
          </View>
        </View>

        {/* --- NAVIGATION MENU --- */}
        <MenuButton 
          title="Weekly Progress" 
          icon="stats-chart" 
          onPress={() => {/* Add Progress Navigation */}} 
        />
        
        <MenuButton 
          title="Progress History" 
          icon="time" 
          onPress={() => {/* Add History Navigation */}} 
        />
        
        <MenuButton 
          title="Account Profile" 
          icon="person" 
          onPress={() => navigation.navigate("StudentProfile")} 
        />

        {/* --- FOOTER --- */}
        <TouchableOpacity 
          style={styles.logoutButton} 
          onPress={() => navigation.replace("Login")}
        >
          <Text style={styles.logoutText}>Log Out</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  // Layout Containers
  safeArea: { 
    flex: 1, 
    backgroundColor: "#F8FAFC" 
  },
  header: { 
    backgroundColor: "#2d8ac7", 
    paddingVertical: 20, 
    alignItems: "center", 
    borderBottomLeftRadius: 20, 
    borderBottomRightRadius: 20 
  },
  overlay: { 
    flex: 1, 
    paddingHorizontal: 20, 
    paddingTop: 20 
  },

  // Typography
  title: { 
    fontSize: 28, 
    fontWeight: "bold", 
    color: "#fff" 
  },
  welcomeContainer: { 
    marginBottom: 20 
  },
  welcomeText: { 
    fontSize: 24, 
    fontWeight: "bold", 
    color: "#1e293b" 
  },
  subtitle: { 
    fontSize: 14, 
    color: "#64748b" 
  },

  // Profile Card
  card: { 
    flexDirection: "row", 
    width: "100%", 
    padding: 20, 
    borderRadius: 20, 
    alignItems: "center", 
    backgroundColor: "#2d8ac7", 
    marginBottom: 30 
  },
  profileImage: { 
    width: 70, 
    height: 70, 
    borderRadius: 35, 
    borderWidth: 3, 
    borderColor: "rgba(255,255,255,0.5)" 
  },
  infoContainer: { 
    marginLeft: 15 
  },
  childName: { 
    fontSize: 20, 
    fontWeight: "bold", 
    color: "#fff" 
  },

  // Menu Buttons
  inputContainer: { 
    flexDirection: "row", 
    alignItems: "center", 
    backgroundColor: "#2d8ac7", 
    borderRadius: 20, 
    paddingHorizontal: 15, 
    paddingVertical: 12, 
    marginBottom: 15, 
    width: "100%" 
  },
  iconCircle: { 
    width: 40, 
    height: 40, 
    borderRadius: 20, 
    backgroundColor: "#fff", 
    justifyContent: "center", 
    alignItems: "center" 
  },
  buttonText: { 
    flex: 1, 
    color: "#fff", 
    fontWeight: "600", 
    fontSize: 16, 
    marginLeft: 15 
  },

  // Footer
  logoutButton: { 
    marginTop: "auto", 
    marginBottom: 20, 
    alignSelf: "center" 
  },
  logoutText: { 
    color: "#ef4444", 
    fontWeight: "bold", 
    fontSize: 16 
  },
});