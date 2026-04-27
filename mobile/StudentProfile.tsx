import React from "react";
import { 
  View, 
  Text, 
  StyleSheet, 
  TouchableOpacity, 
  SafeAreaView, 
  Image, 
  ImageBackground, 
  ScrollView, 
  StatusBar 
} from "react-native";
import { Ionicons } from "@expo/vector-icons";

const InfoRow = ({ label, value, icon }: any) => (
  <View style={styles.infoRow}>
    <View style={styles.labelContainer}>
      <Ionicons name={icon} size={18} color="#2d8ac7" style={styles.rowIcon} />
      <Text style={styles.label}>{label}</Text>
    </View>
    <Text style={styles.value}>{value}</Text>
  </View>
);

export default function StudentProfileScreen({ navigation }: any) {
  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="light-content" backgroundColor="#2d8ac7" />
      
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={28} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Student Profile</Text>
        <View style={{ width: 28 }} /> 
      </View>

      <ScrollView showsVerticalScrollIndicator={false}>
        {/* --- UPDATED BANNER SECTION --- */}
        <ImageBackground 
          source={require("./assets/doodle.jpg")} 
          style={styles.doodleBanner}
          resizeMode="cover"
        >
          {/* Centered Image Wrapper */}
          <View style={styles.imageWrapper}>
            <Image 
              source={require("./assets/cjpic.jpg")} 
              style={styles.profileImage} 
            />
          </View>
        </ImageBackground>

        <View style={styles.content}>
          <View style={styles.infoCard}>
            <View style={styles.cardHeaderContainer}>
              <Ionicons name="person-circle" size={22} color="#2d8ac7" />
              <Text style={styles.cardHeader}>Student Details</Text>
            </View>
            <View style={styles.divider} />
            <InfoRow label="Full Name" value="Michelle Llander" icon="person-outline" />
            <InfoRow label="Age" value="4 Years Old" icon="calendar-outline" />
            <InfoRow label="Gender" value="Female" icon="female-outline" />
            <InfoRow label="Birth Date" value="Jan 1, 2022" icon="gift-outline" />
            <InfoRow label="Nationality" value="Filipino" icon="flag-outline" />
            <InfoRow label="Religion" value="Iglesia Ni Cristo" icon="book-outline" />
          </View>

          <View style={styles.infoCard}>
            <View style={styles.cardHeaderContainer}>
              <Ionicons name="shield-checkmark" size={22} color="#2d8ac7" />
              <Text style={styles.cardHeader}>Guardian Details</Text>
            </View>
            <View style={styles.divider} />
            <InfoRow label="Father" value="Christoper Francisco" icon="business-outline" />
            <InfoRow label="Relationship" value="Parent" icon="heart-outline" />
            <InfoRow label="Email" value="cjfrancisco@mail.com" icon="mail-outline" />
            <InfoRow label="Contact" value="09123456789" icon="call-outline" />
            <InfoRow label="Home Address" value="Montalban, Rizal" icon="home-outline" />
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: "#F0F4F8" },
  header: { 
    backgroundColor: "#2d8ac7", 
    height: 60, 
    flexDirection: "row", 
    alignItems: "center", 
    justifyContent: "space-between", 
    paddingHorizontal: 15,
  },
  headerTitle: { color: "#fff", fontSize: 20, fontWeight: "bold" },
  backButton: { padding: 5 },
  
  // --- UPDATED CENTERING STYLES ---
  doodleBanner: { 
    height: 200, // Increased height slightly for a better "high center" look
    width: "100%", 
    justifyContent: "center", // Vertically centers the content
    alignItems: "center",     // Horizontally centers the content
  },
  imageWrapper: {
    // Removed the large marginTop so it stays inside the banner
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 4,
    elevation: 10,
  },
  profileImage: { 
    width: 120, 
    height: 120, 
    borderRadius: 60, 
    borderWidth: 4, 
    borderColor: "#fff" 
  },
  // --- END OF UPDATED STYLES ---

  content: { padding: 20 },
  infoCard: { 
    backgroundColor: "#fff", 
    borderRadius: 15, 
    padding: 20, 
    marginBottom: 20,
    elevation: 2,
  },
  cardHeaderContainer: { flexDirection: 'row', alignItems: 'center', marginBottom: 10 },
  cardHeader: { fontSize: 17, fontWeight: "bold", color: "#2d8ac7", marginLeft: 8 },
  divider: { height: 1, backgroundColor: "#f0f0f0", marginBottom: 15 },
  infoRow: { flexDirection: "row", justifyContent: "space-between", marginBottom: 12, alignItems: 'center' },
  labelContainer: { flexDirection: 'row', alignItems: 'center' },
  rowIcon: { marginRight: 10, width: 22 },
  label: { fontSize: 13, color: "#7f8c8d", fontWeight: "500" },
  value: { fontSize: 14, color: "#2c3e50", fontWeight: "bold" },
});