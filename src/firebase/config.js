import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyA-GFpVMlWDKMe8aNbzLp0lqoeRfnBdEnU",
  authDomain: "etcapartmentsystem.firebaseapp.com",
  projectId: "etcapartmentsystem",
  storageBucket: "etcapartmentsystem.appspot.com",
  messagingSenderId: "104368065523",
  appId: "YOUR_APP_ID"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

export { auth }; 