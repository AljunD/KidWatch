import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, SafeAreaView, Image, FlatList, StatusBar } from 'react-native';
import { Ionicons } from '@expo/vector-icons';

const STUDENTS = [
  { id: '1', name: 'Llander, Michelle', image: require("./assets/mitch.jpg") },
  { id: '2', name: 'Dalman, Aljun', image: require("./assets/aljun.jpg") }, // Placeholder image
];

export default function SelectStudentScreen({ navigation }: any) {
  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="dark-content" />
      <View style={styles.header}>
        <Text style={styles.welcomeText}>Parent Portal</Text>
        <Text style={styles.title}>Who's learning today?</Text>
      </View>

      <FlatList
        data={STUDENTS}
        contentContainerStyle={styles.list}
        renderItem={({ item }) => (
          <TouchableOpacity 
            style={styles.card}
            onPress={() => navigation.navigate('Dashboard', { 
              studentName: item.name,
              studentImage: item.image 
            })}
          >
            <Image source={item.image} style={styles.avatar} />
            <View style={styles.info}>
              <Text style={styles.nameText}>{item.name}</Text>
              <Text style={styles.subText}>View Progress Records</Text>
            </View>
            <View style={styles.arrowContainer}>
              <Ionicons name="chevron-forward" size={20} color="#4A90E2" />
            </View>
          </TouchableOpacity>
        )}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#F0F9FF' },
  header: { padding: 30, marginTop: 20 },
  welcomeText: { fontSize: 13, color: '#94a3b8', fontWeight: '900', letterSpacing: 1.5, textTransform: 'uppercase' },
  title: { fontSize: 26, fontWeight: '900', color: '#1A365D', marginTop: 5 },
  list: { paddingHorizontal: 20 },
  card: { 
    backgroundColor: '#fff', 
    flexDirection: 'row', 
    alignItems: 'center', 
    padding: 18, 
    borderRadius: 25, 
    marginBottom: 15,
    borderWidth: 2,
    borderColor: '#e2e8f0',
    elevation: 3
  },
  avatar: { width: 65, height: 65, borderRadius: 20, borderWidth: 2, borderColor: '#F0F9FF' },
  info: { flex: 1, marginLeft: 15 },
  nameText: { fontSize: 17, fontWeight: '900', color: '#1A365D' },
  subText: { fontSize: 13, color: '#64748B', marginTop: 2 },
  arrowContainer: { backgroundColor: '#F0F9FF', padding: 8, borderRadius: 12 }
});