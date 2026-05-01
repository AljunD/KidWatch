import React from "react";
import { 
  View, 
  Text, 
  StyleSheet, 
  ImageBackground, 
  Image, 
  TouchableOpacity 
} from "react-native";

export default function WelcomeScreen({ navigation }: any) {
  return (
    <ImageBackground 
      source={require("./assets/back.jpg")} 
      style={styles.container}
    >
      <View style={styles.content}>
        
        {/* --- BRANDING SECTION --- */}
        <Image 
          source={require("./assets/logo.png")} 
          style={styles.logo} 
          resizeMode="contain" 
        />
        
        {/* --- ACTION SECTION --- */}
        <TouchableOpacity 
          style={styles.button} 
          onPress={() => navigation.navigate("Login")}
          activeOpacity={0.8}
        >
          <Text style={styles.buttonText}>Let's get started</Text>
        </TouchableOpacity>

      </View>
    </ImageBackground>
  );
}

const styles = StyleSheet.create({
  // Layout
  container: { 
    flex: 1 
  },
  content: { 
    flex: 1, 
    alignItems: 'center', 
    justifyContent: 'center', 
    paddingBottom: 50 
  },

  // Branding
  logo: { 
    width: 250, 
    height: 250, 
    marginBottom: 20 
  },

  // Interactive Elements
  button: { 
    backgroundColor: "#3498db", 
    paddingVertical: 15, 
    paddingHorizontal: 60, 
    borderRadius: 30,
    elevation: 5, // Adds a subtle shadow for Android
    shadowColor: '#000', // Adds a subtle shadow for iOS
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
  },
  buttonText: { 
    color: "#fff", 
    fontSize: 18, 
    fontWeight: "600" 
  },
});