// App.js
import React, { useState } from 'react';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import Login from './components/Login';
import Register from './components/Register';
import Dashboard from './components/Dashboard';
// ... autres imports
import AdminDashboard from './components/Dashboards/AdminDashboard';
// ...
import './App.css';

const AuthApp = () => {
  const [activeForm, setActiveForm] = useState('register');
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="app">
        <div className="loading-container">
          <div className="loading-spinner"></div>
          <p>Chargement...</p>
        </div>
      </div>
    );
  }

  // Rediriger vers l'admin si l'utilisateur est admin
  if (user && user.type === 'admin') {
    return <AdminDashboard />;
  }

  // Rediriger vers le dashboard utilisateur normal
  if (user) {
    return <Dashboard />;
  }

  return (
    <div className="app">
      <div className={`auth-container ${activeForm === 'login' ? 'login-active' : 'register-active'}`}>
        <div className="auth-card">
          {/* Toggle Switch */}
          <div className="toggle-switch">
            <div 
              className={`toggle-option ${activeForm === 'login' ? 'active' : ''}`}
              onClick={() => setActiveForm('login')}
            >
              <span className="toggle-icon">🔐</span>
              <span className="toggle-text">Se connecter</span>
            </div>
            <div 
              className={`toggle-option ${activeForm === 'register' ? 'active' : ''}`}
              onClick={() => setActiveForm('register')}
            >
              <span className="toggle-icon">👤</span>
              <span className="toggle-text">Créer un compte</span>
            </div>
            <div className={`toggle-slider ${activeForm}`}></div>
          </div>

          {/* Header */}
          <div className="auth-header">
            <h1>
              {activeForm === 'register' ? 'Rejoignez notre plateforme' : 'Content de vous revoir !'}
            </h1>
            <p>
              {activeForm === 'register' 
                ? 'Créez votre compte et commencez votre parcours' 
                : 'Connectez-vous à votre compte pour continuer'
              }
            </p>
          </div>

          {/* Form */}
          <div className="form-container">
            {activeForm === 'register' ? (
              <Register onToggleForm={() => setActiveForm('login')} />
            ) : (
              <Login onToggleForm={() => setActiveForm('register')} />
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

const App = () => {
  return (
    <AuthProvider>
      <AuthApp />
    </AuthProvider>
  );
};

export default App;