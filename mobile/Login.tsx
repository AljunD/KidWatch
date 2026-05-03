import React, { useState } from "react"; // Added useState
import {
  ImageBackground,
  Text,
  TextInput,
  TouchableOpacity,
  View,
  StyleSheet,
  SafeAreaView,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";

export default function LoginScreen({ navigation }: any) {
  // State to toggle password visibility
  const [passwordVisible, setPasswordVisible] = useState(false);

  return (
    <ImageBackground 
      source={require("./assets/back.jpg")} 
      style={styles.background} 
      resizeMode="cover"
    >
      <SafeAreaView style={styles.safeArea}>
        
        {/* --- TOP NAVIGATION --- */}
        <TouchableOpacity 
          style={styles.backButton} 
          onPress={() => navigation.goBack()}
        >
          <Ionicons name="chevron-back" size={32} color="#fff" />
        </TouchableOpacity>

        {/* --- MAIN CONTENT --- */}
        <View style={styles.overlay}>
          <View style={styles.card}>
            
            {/* Header Text */}
            <Text style={styles.title}>Welcome Back!</Text>
            <Text style={styles.subtitle}>
              Track your child's weekly learning progress
            </Text>

            {/* Username Input */}
            <View style={styles.inputContainer}>
              <TextInput
                placeholder="Username"
                placeholderTextColor="#888"
                style={styles.input}
              />
              <Ionicons name="person-outline" size={20} color="#555" />
            </View>

            {/* Password Input with Clickable Eye */}
            <View style={styles.inputContainer}>
              <TextInput
                placeholder="Password"
                placeholderTextColor="#888"
                secureTextEntry={!passwordVisible} // Changes based on state
                style={styles.input}
              />
              <TouchableOpacity 
                onPress={() => setPasswordVisible(!passwordVisible)}
              >
                <Ionicons 
                  name={passwordVisible ? "eye-off-outline" : "eye-outline"} 
                  size={20} 
                  color="#555" 
                />
              </TouchableOpacity>
            </View>

            {/* Action Buttons */}
            <TouchableOpacity 
              style={styles.button} 
              onPress={() => navigation.navigate("Dashboard")}
            >
              <Text style={styles.buttonText}>LOGIN</Text>
            </TouchableOpacity>

            <TouchableOpacity onPress={() => {/* Handle Forget Password */}}>
              <Text style={styles.forgot}>Forget Password?</Text>
            </TouchableOpacity>

          </View>
        </View>
        
      </SafeAreaView>
    </ImageBackground>
  );
}

const styles = StyleSheet.create({
  background: { 
    flex: 1 
  },
  safeArea: { 
    flex: 1 
  },
  overlay: { 
    flex: 1, 
    justifyContent: "center", 
    alignItems: "center", 
    paddingHorizontal: 20, 
    marginTop: -60 
  },
  backButton: { 
    padding: 20, 
    width: 60 
  },
  card: { 
    width: "100%", 
    padding: 25, 
    borderRadius: 20, 
    alignItems: "center" 
  },
  title: { 
    fontSize: 25, 
    fontWeight: "bold", 
    color: "#fff", 
    marginBottom: 8 
  },
  subtitle: { 
    fontSize: 15, 
    color: "#f0f0f0", 
    textAlign: "center", 
    marginBottom: 25 
  },
  inputContainer: { 
    flexDirection: "row", 
    alignItems: "center", 
    backgroundColor: "#e5e5e5", 
    borderRadius: 25, 
    paddingHorizontal: 15, 
    paddingVertical: 12, 
    marginBottom: 15, 
    width: "100%", 
    justifyContent: "space-between" 
  },
  input: { 
    flex: 1, 
    color: "#000" 
  },
  button: { 
    backgroundColor: "#2d8ac7", 
    paddingVertical: 14, 
    borderRadius: 25, 
    width: "100%", 
    alignItems: "center", 
    marginTop: 10 
  },
  buttonText: { 
    color: "#fff", 
    fontWeight: "bold", 
    letterSpacing: 1 
  },
  forgot: { 
    marginTop: 15, 
    color: "#919191", 
    fontSize: 12 
  },
});