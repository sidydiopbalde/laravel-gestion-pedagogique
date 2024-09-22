// Importer les fonctions nécessaires des SDK Firebase
import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider, signInWithPopup } from "firebase/auth";

// Configuration de votre application Firebase
const firebaseConfig = {
  apiKey: "AIzaSyB28D2naJ7c0ZUHX_A4b20cmQCoWgAn6Q0",
  authDomain: "laravel-gestion-pedagogique.firebaseapp.com",
  databaseURL: "https://laravel-gestion-pedagogique-default-rtdb.firebaseio.com",
  projectId: "laravel-gestion-pedagogique",
  storageBucket: "laravel-gestion-pedagogique.appspot.com",
  messagingSenderId: "359637117947",
  appId: "1:359637117947:web:ae30fa7046171640eef95e",
  measurementId: "G-73QTGBKR5B"
};

// Initialiser Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

// Fonction pour déclencher l'authentification Google
const googleLogin = () => {
  const provider = new GoogleAuthProvider();

  signInWithPopup(auth, provider)
    .then((result) => {
      // Obtenir le Google ID Token
      const idToken = result._tokenResponse.idToken;
      console.log('Google ID Token:', idToken);
      // Envoyer le token au backend
      fetch('/api/auth/google', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ google_id_token: idToken })
      })
      .then(response => response.json())
      .then(data => {
        if (data.token) {
          console.log('Authenticated with Firebase:', data.token);
          // Vous pouvez sauvegarder le token dans localStorage ou autre pour les prochaines requêtes
        }
      })
      .catch(error => {
        console.error('Error during authentication:', error);
      });
    })
    .catch((error) => {
      console.error('Google Sign-In error:', error);
    });
};

// Attacher la fonction googleLogin à un bouton
document.getElementById('google-login-btn').addEventListener('click', googleLogin);
