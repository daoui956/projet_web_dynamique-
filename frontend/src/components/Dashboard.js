// src/components/Dashboard.js
import React from 'react';
import { useAuth } from '../contexts/AuthContext';
import StudentDashboard from './Dashboards/StudentDashboard';
import TeacherDashboard from './Dashboards/TeacherDashboard';
import AdminDashboard from './Dashboards/AdminDashboard';
import EnterpriseDashboard from './Dashboards/EnterpriseDashboard';

const Dashboard = () => {
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    await logout();
  };

  const renderDashboard = () => {
    switch (user?.type) {
      case 'student':
        return <StudentDashboard user={user} onLogout={handleLogout} />;
      case 'teacher':
        return <TeacherDashboard user={user} onLogout={handleLogout} />;
        case 'enterprise':
  return <EnterpriseDashboard user={user} onLogout={handleLogout} />;

      case 'admin':
        return <AdminDashboard user={user} onLogout={handleLogout} />;
      default:
        return (
          <div style={{ padding: '20px', textAlign: 'center' }}>
            <h2>Type d'utilisateur non reconnu: {user?.type}</h2>
            <button onClick={handleLogout}>Déconnexion</button>
          </div>
        );
    }
  };

  return (
    <div>
      {renderDashboard()}
    </div>
  );
};

export default Dashboard;