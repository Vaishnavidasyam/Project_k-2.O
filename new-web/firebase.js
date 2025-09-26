// firebase.js
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-analytics.js";
import { getDatabase } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-database.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-auth.js";

const firebaseConfig = {
  apiKey: "AIzaSyDmnmnmXMJOkqUPGJFAxTCjWapxM3DjxIU",
  authDomain: "classx-f7eaf.firebaseapp.com",
  databaseURL: "https://classx-f7eaf-default-rtdb.firebaseio.com",
  projectId: "classx-f7eaf",
  storageBucket: "classx-f7eaf.firebasestorage.app",
  messagingSenderId: "174092892914",
  appId: "1:174092892914:web:7bcc25aa8e474d4e2cee6b",
  measurementId: "G-2M0W8WGR28"
};

const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const db = getDatabase(app);
const auth = getAuth(app);

console.log("Firebase has been initialized!");

export { app, analytics, db, auth };
