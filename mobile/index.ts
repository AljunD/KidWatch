import { registerRootComponent } from 'expo';
import App from './App'; // This imports your navigation setup from App.tsx

// This tells Expo to start from App.tsx instead of Login.tsx
registerRootComponent(App);