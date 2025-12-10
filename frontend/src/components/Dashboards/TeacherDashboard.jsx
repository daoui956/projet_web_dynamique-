// src/components/Dashboards/TeacherDashboard.js
import React, { useState, useEffect } from 'react';
import './TeacherDashboard.css';

const TeacherDashboard = ({ user, onLogout }) => {
  const [activeSection, setActiveSection] = useState('accueil');
  const [dashboardStats, setDashboardStats] = useState(null);
  const [courses, setCourses] = useState([]);
  const [students, setStudents] = useState([]);
  const [assignments, setAssignments] = useState([]);
  const [grades, setGrades] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [apiStatus, setApiStatus] = useState('loading'); // 'loading', 'success', 'error'
  
  // Données mockées en cas d'échec API
  const mockCourses = [
    { id: 1, title: 'Algorithmique Avancée', students_count: 42, assignments_count: 3, description: 'Cours avancé sur les algorithmes', created_at: '01/12/2024' },
    { id: 2, title: 'Structures de Données', students_count: 35, assignments_count: 2, description: 'Étude des structures de données', created_at: '15/11/2024' }
  ];

  const mockStudents = [
    { id: 1, name: 'Jean Dupont', email: 'jean.dupont@email.com', avatar: 'J' },
    { id: 2, name: 'Marie Martin', email: 'marie.martin@email.com', avatar: 'M' },
    { id: 3, name: 'Pierre Dubois', email: 'pierre.dubois@email.com', avatar: 'P' },
    { id: 4, name: 'Sophie Lambert', email: 'sophie.lambert@email.com', avatar: 'S' }
  ];

  const mockAssignments = [
    { id: 1, title: 'TP1 Algorithmique', description: 'Travail pratique sur les tris', course_title: 'Algorithmique Avancée', due_date: '15/12/2024', is_graded: false },
    { id: 2, title: 'Examen Structures', description: 'Examen final sur les arbres', course_title: 'Structures de Données', due_date: '20/12/2024', is_graded: true },
    { id: 3, title: 'Projet Java', description: 'Projet de développement', course_title: 'Programmation Orientée Objet', due_date: '10/01/2025', is_graded: false }
  ];

  const mockStats = {
    teacher: { name: user.name, email: user.email },
    stats: { courses: 2, students: 77, assignments_to_grade: 12 }
  };

  // Vérifier la connexion API
  const checkApiConnection = async () => {
    try {
      // Essayer de se connecter à l'API Laravel
      const response = await fetch('http://localhost:8000/sanctum/csrf-cookie', {
        credentials: 'include'
      });
      setApiStatus(response.ok ? 'success' : 'error');
    } catch (err) {
      setApiStatus('error');
    }
  };

  // Simuler les appels API avec timeout
  const simulateApiCall = (data, delay = 1000) => {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({ data: { success: true, ...data } });
      }, delay);
    });
  };

  // Charger les données selon la section active
  useEffect(() => {
    const loadData = async () => {
      setLoading(true);
      setError(null);
      
      try {
        // D'abord vérifier la connexion API
        await checkApiConnection();
        
        // Simuler le chargement des données
        await new Promise(resolve => setTimeout(resolve, 500));
        
        if (apiStatus === 'error') {
          // Utiliser les données mockées
          switch (activeSection) {
            case 'accueil':
              setDashboardStats(mockStats);
              break;
            case 'mes-cours':
              setCourses(mockCourses);
              break;
            case 'eleves':
              setStudents(mockStudents);
              break;
            case 'devoirs':
              setAssignments(mockAssignments);
              break;
            case 'notes':
              setGrades([
                { id: 1, student: 'Jean Dupont', assignment: 'TP1 Algorithmique', score: 15.5, date: '10/12/2024' },
                { id: 2, student: 'Marie Martin', assignment: 'TP1 Algorithmique', score: 18, date: '10/12/2024' },
                { id: 3, student: 'Pierre Dubois', assignment: 'Examen Structures', score: 12, date: '15/12/2024' }
              ]);
              break;
            default:
              break;
          }
        } else {
          // Ici, vous intégrerez les vrais appels API plus tard
          // Pour l'instant, utiliser les données mockées
          switch (activeSection) {
            case 'accueil':
              setDashboardStats(mockStats);
              break;
            case 'mes-cours':
              setCourses(mockCourses);
              break;
            case 'eleves':
              setStudents(mockStudents);
              break;
            case 'devoirs':
              setAssignments(mockAssignments);
              break;
            default:
              break;
          }
        }
      } catch (err) {
        console.error('Erreur lors du chargement:', err);
        setError('Impossible de charger les données. Utilisation des données de démonstration.');
      } finally {
        setLoading(false);
      }
    };

    loadData();
  }, [activeSection]);

  // Rendu du contenu principal
  const renderContent = () => {
    if (loading) {
      return (
        <div className="loading-section">
          <div className="spinner"></div>
          <p>Chargement en cours...</p>
          {apiStatus === 'error' && (
            <p className="api-warning">⚠️ Mode démonstration (API non connectée)</p>
          )}
        </div>
      );
    }

    switch (activeSection) {
      case 'mes-cours':
        return (
          <div className="content-section">
            <div className="section-header">
              <h2>Mes Cours</h2>
              <button className="btn btn-primary" onClick={() => alert('Fonctionnalité à implémenter')}>
                + Nouveau Cours
              </button>
            </div>
            
            {apiStatus === 'error' && (
              <div className="demo-notice">
                <p>⚠️ Mode démonstration - Les données ne sont pas enregistrées</p>
              </div>
            )}
            
            <div className="courses-grid">
              {courses.map(course => (
                <div key={course.id} className="course-card">
                  <h3>{course.title}</h3>
                  <p className="course-description">{course.description}</p>
                  <div className="course-meta">
                    <span>👨‍🎓 {course.students_count} étudiants</span>
                    <span>📝 {course.assignments_count} devoirs</span>
                  </div>
                  <div className="course-footer">
                    <p>Créé le: {course.created_at}</p>
                  </div>
                  <div className="course-actions">
                    <button className="btn btn-primary" onClick={() => {
                      setActiveSection('eleves');
                      alert(`Ouverture des étudiants pour: ${course.title}`);
                    }}>
                      Voir les étudiants
                    </button>
                    <button className="btn btn-secondary" onClick={() => alert('Statistiques')}>
                      Statistiques
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        );

      case 'eleves':
        return (
          <div className="content-section">
            <div className="section-header">
              <h2>Liste des Élèves</h2>
              <select className="course-select" onChange={(e) => {
                alert(`Chargement des étudiants du cours: ${e.target.value}`);
              }}>
                <option value="">Sélectionner un cours</option>
                {courses.map(course => (
                  <option key={course.id} value={course.id}>
                    {course.title}
                  </option>
                ))}
              </select>
            </div>
            
            <div className="students-list">
              {students.map(student => (
                <div key={student.id} className="student-item">
                  <div className="student-avatar">
                    {student.avatar}
                  </div>
                  <div className="student-info">
                    <h4>{student.name}</h4>
                    <p>{student.email}</p>
                  </div>
                  <div className="student-actions">
                    <button className="btn btn-primary btn-sm" onClick={() => alert(`Notes de ${student.name}`)}>
                      Notes
                    </button>
                    <button className="btn btn-secondary btn-sm" onClick={() => alert(`Contact: ${student.email}`)}>
                      Contact
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        );

      case 'devoirs':
        return (
          <div className="content-section">
            <div className="section-header">
              <h2>Devoirs</h2>
              <div className="header-actions">
                <button className="btn btn-primary" onClick={() => alert('Créer un nouveau devoir')}>
                  + Nouveau Devoir
                </button>
                <div className="filter-buttons">
                  <button className="btn-filter active">Tous</button>
                  <button className="btn-filter">À corriger</button>
                  <button className="btn-filter">Corrigés</button>
                </div>
              </div>
            </div>
            
            <div className="assignments-grid">
              {assignments.map(assignment => (
                <div key={assignment.id} className="assignment-card">
                  <div className="assignment-header">
                    <h3>{assignment.title}</h3>
                    <span className={`status-badge ${assignment.is_graded ? 'graded' : 'pending'}`}>
                      {assignment.is_graded ? '✓ Corrigé' : '⏱ À corriger'}
                    </span>
                  </div>
                  <p className="assignment-description">{assignment.description}</p>
                  <div className="assignment-meta">
                    <span>📚 {assignment.course_title}</span>
                    <span>📅 Échéance: {assignment.due_date}</span>
                  </div>
                  <div className="assignment-actions">
                    <button className="btn btn-primary" onClick={() => {
                      const score = prompt("Note (sur 20):");
                      if (score) alert(`Note ${score}/20 enregistrée`);
                    }}>
                      Corriger
                    </button>
                    <button className="btn btn-secondary" onClick={() => alert('Détails du devoir')}>
                      Détails
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        );

      case 'notes':
        return (
          <div className="content-section">
            <div className="section-header">
              <h2>Notes des Étudiants</h2>
              <button className="btn btn-primary" onClick={() => alert('Export des notes')}>
                Exporter les notes
              </button>
            </div>
            
            <div className="demo-message">
              <p>Cette section affichera les notes réelles une fois l'API connectée.</p>
            </div>
            
            <div className="grades-table">
              <table>
                <thead>
                  <tr>
                    <th>Étudiant</th>
                    <th>Devoir</th>
                    <th>Note</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Jean Dupont</td>
                    <td>TP1 Algorithmique</td>
                    <td className="grade-value pass">15.5/20</td>
                    <td>10/12/2024</td>
                    <td>
                      <button className="btn btn-sm btn-primary" onClick={() => alert('Modifier note')}>
                        Modifier
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Marie Martin</td>
                    <td>TP1 Algorithmique</td>
                    <td className="grade-value pass">18/20</td>
                    <td>10/12/2024</td>
                    <td>
                      <button className="btn btn-sm btn-primary">Modifier</button>
                    </td>
                  </tr>
                  <tr>
                    <td>Pierre Dubois</td>
                    <td>Examen Structures</td>
                    <td className="grade-value fail">12/20</td>
                    <td>15/12/2024</td>
                    <td>
                      <button className="btn btn-sm btn-primary">Modifier</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        );

      case 'emploi-temps':
        return (
          <div className="content-section">
            <div className="section-header">
              <h2>Emploi du Temps</h2>
              <button className="btn btn-primary" onClick={() => alert('Ajouter un cours à l\'emploi du temps')}>
                + Ajouter un cours
              </button>
            </div>
            
            <div className="schedule-grid">
              {[
                { id: 1, day: 'Lundi', time: '08:00 - 10:00', course: 'Algorithmique', room: 'A201' },
                { id: 2, day: 'Mardi', time: '10:00 - 12:00', course: 'Structures de Données', room: 'B105' },
                { id: 3, day: 'Mercredi', time: '14:00 - 16:00', course: 'Algorithmique', room: 'A201' },
                { id: 4, day: 'Jeudi', time: '09:00 - 11:00', course: 'Programmation', room: 'C302' },
                { id: 5, day: 'Vendredi', time: '13:00 - 15:00', course: 'Structures de Données', room: 'B105' }
              ].map(item => (
                <div key={item.id} className="schedule-card">
                  <div className="schedule-header">
                    <h3>{item.day}</h3>
                    <span className="time-badge">{item.time}</span>
                  </div>
                  <div className="schedule-body">
                    <p><strong>Cours:</strong> {item.course}</p>
                    <p><strong>Salle:</strong> {item.room}</p>
                  </div>
                  <div className="schedule-actions">
                    <button className="btn btn-sm btn-secondary" onClick={() => alert(`Modifier ${item.course}`)}>
                      Modifier
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        );

      case 'accueil':
      default:
        return (
          <div className="welcome-section">
            <h1>Bienvenue, {user.name} ! 👨‍🏫</h1>
            <p>Vous êtes connecté en tant qu'enseignant</p>
            
            {apiStatus === 'error' && (
              <div className="api-alert">
                <p>⚠️ Mode démonstration - Connectez le backend pour les données réelles</p>
              </div>
            )}
            
            <div className="stats-cards">
              <div className="stat-card">
                <h3>{dashboardStats?.stats?.courses || 2}</h3>
                <p>Cours Enseignés</p>
              </div>
              <div className="stat-card">
                <h3>{dashboardStats?.stats?.students || 77}</h3>
                <p>Étudiants</p>
              </div>
              <div className="stat-card">
                <h3>{dashboardStats?.stats?.assignments_to_grade || 12}</h3>
                <p>Devoirs à Corriger</p>
              </div>
            </div>

            <div className="quick-actions">
              <button className="quick-action-btn" onClick={() => setActiveSection('mes-cours')}>
                📚 Voir mes cours
              </button>
              <button className="quick-action-btn" onClick={() => setActiveSection('devoirs')}>
                📝 Gérer les devoirs
              </button>
              <button className="quick-action-btn" onClick={() => alert('Créer un nouveau cours')}>
                ➕ Créer un nouveau cours
              </button>
              <button className="quick-action-btn" onClick={() => setActiveSection('notes')}>
                📊 Consulter les notes
              </button>
            </div>

            <div className="recent-activities">
              <h3>Activités récentes</h3>
              <ul>
                <li>✅ Vous avez corrigé le devoir "TP1 Algorithmique"</li>
                <li>📅 Nouveau cours "Intelligence Artificielle" créé hier</li>
                <li>👨‍🎓 5 nouveaux étudiants inscrits cette semaine</li>
                <li>📝 Devoir "Examen final" à corriger avant le 20/12</li>
              </ul>
            </div>
          </div>
        );
    }
  };

  return (
    <div className="teacher-dashboard">
      <div className="sidebar">
        <div className="user-info">
          <div className="avatar">{user.name.charAt(0)}</div>
          <h3>{user.name}</h3>
          <p>Enseignant</p>
        </div>

        <nav className="nav-menu">
          <button 
            className={activeSection === 'accueil' ? 'active' : ''} 
            onClick={() => setActiveSection('accueil')}
          >
            🏠 Accueil
          </button>
          <button 
            className={activeSection === 'mes-cours' ? 'active' : ''} 
            onClick={() => setActiveSection('mes-cours')}
          >
            📚 Mes Cours
          </button>
          <button 
            className={activeSection === 'eleves' ? 'active' : ''} 
            onClick={() => setActiveSection('eleves')}
          >
            👨‍🎓 Élèves
          </button>
          <button 
            className={activeSection === 'devoirs' ? 'active' : ''} 
            onClick={() => setActiveSection('devoirs')}
          >
            📝 Devoirs
          </button>
          <button 
            className={activeSection === 'notes' ? 'active' : ''} 
            onClick={() => setActiveSection('notes')}
          >
            📊 Notes
          </button>
          <button 
            className={activeSection === 'emploi-temps' ? 'active' : ''} 
            onClick={() => setActiveSection('emploi-temps')}
          >
            📅 Emploi du Temps
          </button>
          <button className="logout" onClick={onLogout}>
            🚪 Déconnexion
          </button>
        </nav>
      </div>

      <div className="main-content">
        <header className="header">
          <h1>Tableau de Bord Enseignant</h1>
          <div className="header-info">
            <span className="welcome-text">Bienvenue, {user.name}</span>
            <span className="date-info">{new Date().toLocaleDateString('fr-FR', { 
              weekday: 'long', 
              year: 'numeric', 
              month: 'long', 
              day: 'numeric' 
            })}</span>
          </div>
        </header>
        
        <div className="content-area">
          {renderContent()}
        </div>
      </div>
    </div>
  );
};

export default TeacherDashboard;