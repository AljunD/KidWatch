import React from "react";
import {
  View,
  Text,
  StyleSheet,
  ImageBackground,
  Image,
  TouchableOpacity,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";

export default function WelcomeScreen({ navigation }: any) {
  return (
    <ImageBackground
      source={require("./assets/back.jpg")}
      style={styles.container}
      resizeMode="cover"
    >
      <SafeAreaView style={styles.safeArea}>
        <View style={styles.content}>
          {/* Branding Logo */}
          <Image
            source={require("./assets/logo.png")}
            style={styles.logo}
            resizeMode="contain"
          />

          {/* CTA Button */}
          <TouchableOpacity
            style={styles.button}
            onPress={() => navigation.navigate("Login")}
            activeOpacity={0.85}
          >
            <Text style={styles.buttonText}>Let's Get Started</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    </ImageBackground>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1 },
  container: { flex: 1 },

  content: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    paddingHorizontal: 20,
    paddingBottom: 60,
  },

  // Branding
  logo: {
    width: 240,
    height: 240,
    marginBottom: 30,
  },

  // Button Styling
  button: {
    backgroundColor: "#3498db",
    paddingVertical: 16,
    paddingHorizontal: 60,
    borderRadius: 30,
    elevation: 6,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.25,
    shadowRadius: 4,
  },
  buttonText: {
    color: "#fff",
    fontSize: 18,
    fontWeight: "700",
    letterSpacing: 1,
    textTransform: "uppercase",
  },
});
