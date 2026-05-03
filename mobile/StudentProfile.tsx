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

// Reusable Bubble Row
const InfoRow = ({ label, value, icon, color }: any) => (
  <View style={styles.infoRow}>
    <View style={styles.labelContainer}>
      <View style={[styles.iconCircle, { backgroundColor: color + "15" }]}>
        <Ionicons name={icon} size={18} color={color} />
      </View>
      <Text style={styles.label}>{label}</Text>
    </View>
    <Text style={styles.value}>{value}</Text>
  </View>
);

export default function StudentProfileScreen({ navigation }: any) {
  return (
    <SafeAreaView style={styles.safeArea}>
      {/* Light Status Bar for the Cloud background */}
      <StatusBar barStyle="dark-content" />
      
      {/* --- KID THEME HEADER --- */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color="#1A365D" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Student Profile</Text>
        <View style={{ width: 40 }} /> 
      </View>

      <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: 40 }}>
        
        {/* --- BANNER WITH BUBBLE IMAGE --- */}
        <ImageBackground 
          source={require("./assets/doodle.jpg")} 
          style={styles.doodleBanner}
          imageStyle={{ borderBottomLeftRadius: 40, borderBottomRightRadius: 40 }}
          resizeMode="cover"
        >
          <View style={styles.imageWrapper}>
            <Image 
              source={require("./assets/cjpic.jpg")} 
              style={styles.profileImage} 
            />
          </View>
        </ImageBackground>

        <View style={styles.content}>
          {/* --- STUDENT DETAILS CARD --- */}
          <View style={styles.infoCard}>
            <View style={styles.cardHeaderContainer}>
              <View style={styles.headerIconCircle}>
                <Ionicons name="person" size={20} color="#fff" />
              </View>
              <Text style={styles.cardHeader}>Student Details</Text>
            </View>
            
            <InfoRow label="Full Name" value="Michelle Llander" icon="person-outline" color="#FF6B6B" />
            <InfoRow label="Age" value="4 Years Old" icon="calendar-outline" color="#4A90E2" />
            <InfoRow label="Gender" value="Female" icon="female-outline" color="#FFBE0B" />
            <InfoRow label="Birth Date" value="Jan 1, 2022" icon="gift-outline" color="#4ECDC4" />
            <InfoRow label="Nationality" value="Filipino" icon="flag-outline" color="#FF9F1C" />
            <InfoRow label="Religion" value="Iglesia Ni Cristo" icon="book-outline" color="#9b59b6" />
          </View>

          {/* --- GUARDIAN DETAILS CARD --- */}
          <View style={styles.infoCard}>
            <View style={styles.cardHeaderContainer}>
              <View style={[styles.headerIconCircle, { backgroundColor: '#4ECDC4' }]}>
                <Ionicons name="heart" size={20} color="#fff" />
              </View>
              <Text style={styles.cardHeader}>Guardian Details</Text>
            </View>
            
            <InfoRow label="Name" value="Christoper Francisco" icon="business-outline" color="#4A90E2" />
            <InfoRow label="Relationship" value="Parent" icon="people-outline" color="#FF6B6B" />
            <InfoRow label="Email" value="cjfrancisco@mail.com" icon="mail-outline" color="#4ECDC4" />
            <InfoRow label="Contact" value="09123456789" icon="call-outline" color="#FFBE0B" />
            <InfoRow label="Home Address" value="Montalban, Rizal" icon="home-outline" color="#FF9F1C" />
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: "#F0F9FF" }, // Matches Dashboard
  header: { 
    height: 70, 
    flexDirection: "row", 
    alignItems: "center", 
    justifyContent: "space-between", 
    paddingHorizontal: 20,
    backgroundColor: '#F0F9FF'
  },
  headerTitle: { color: "#1A365D", fontSize: 20, fontWeight: "900" },
  backButton: { 
    backgroundColor: '#fff', 
    padding: 8, 
    borderRadius: 15,
    elevation: 2,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowRadius: 5,
  },
  
  doodleBanner: { 
    height: 180, 
    width: "100%", 
    justifyContent: "center", 
    alignItems: "center",
    backgroundColor: '#2d8ac7',
    borderBottomLeftRadius: 40,
    borderBottomRightRadius: 40,
    overflow: 'hidden',
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
    borderRadius: 35, // Squircle to match Dashboard
    borderWidth: 4, 
    borderColor: "#fff" 
  },

  content: { padding: 20, marginTop: 10 },
  infoCard: { 
    backgroundColor: "#fff", 
    borderRadius: 30, // Bubble effect
    padding: 22, 
    marginBottom: 20,
    elevation: 4,
    shadowColor: "#000",
    shadowOpacity: 0.05,
    shadowRadius: 10,
  },
  cardHeaderContainer: { flexDirection: 'row', alignItems: 'center', marginBottom: 20 },
  headerIconCircle: {
    width: 36,
    height: 36,
    borderRadius: 12,
    backgroundColor: '#FF6B6B',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  cardHeader: { fontSize: 18, fontWeight: "900", color: "#1A365D" },
  
  infoRow: { 
    flexDirection: "row", 
    justifyContent: "space-between", 
    marginBottom: 16, 
    alignItems: 'center' 
  },
  labelContainer: { flexDirection: 'row', alignItems: 'center' },
  iconCircle: {
    width: 32,
    height: 32,
    borderRadius: 10,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 10,
  },
  label: { fontSize: 13, color: "#94a3b8", fontWeight: "700" },
  value: { fontSize: 14, color: "#1A365D", fontWeight: "800" },
});