// src/components/Dashboards/StudentDashboard.js
import React, { useState, useEffect } from 'react';
import './StudentDashboard.css';
import api from '../../services/api';

const StudentDashboard = ({ user, onLogout }) => {
  const [activeSection, setActiveSection] = useState('accueil');
  const [searchTerm, setSearchTerm] = useState('');

  // États pour les données
  const [courses, setCourses] = useState([]);
  const [events, setEvents] = useState([]);
  const [forumPosts, setForumPosts] = useState([]);
  const [attestations, setAttestations] = useState([]);
  const [studentProfile, setStudentProfile] = useState(null);

  // États pour les formulaires
  const [newPost, setNewPost] = useState({ title: '', content: '' });
  const [attestationRequest, setAttestationRequest] = useState({ type: 'scolaire', reason: '' });
  const [profileForm, setProfileForm] = useState({ description: '', interests: '', cv: null });

  // États de chargement et d'erreur
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

// ...existing code...

  // Fonction pour charger les posts du forum
  const fetchForumPosts = async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await api.get('/forum-posts');
      // Vérifier la structure de la réponse
      const posts = response.data.data || response.data;
      setForumPosts(Array.isArray(posts) ? posts : []);
    } catch (err) {
      console.error('Erreur forum:', err);
      setError('Erreur lors du chargement du forum');
      setForumPosts([]);
    } finally {
      setLoading(false);
    }
  };

  // Fonction pour charger les cours
  const fetchCourses = async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await api.get('/courses');
      const courses = response.data.data || response.data;
      setCourses(Array.isArray(courses) ? courses : []);
    } catch (err) {
      console.error('Erreur cours:', err);
      setError('Erreur lors du chargement des cours');
      setCourses([]);
    } finally {
      setLoading(false);
    }
  };

  // Fonction pour charger les événements
  const fetchEvents = async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await api.get('/events');
      const events = response.data.data || response.data;
      setEvents(Array.isArray(events) ? events : []);
    } catch (err) {
      console.error('Erreur événements:', err);
      setError('Erreur lors du chargement des événements');
      setEvents([]);
    } finally {
      setLoading(false);
    }
  };

  // Gestion de la soumission d'un nouveau post
  const handleSubmitPost = async () => {
    if (!newPost.title || !newPost.content) {
      alert('Veuillez remplir le titre et le contenu');
      return;
    }

    setLoading(true);
    setError(null);
    try {
      const response = await api.post('/forum-posts', newPost);
      const newPostData = response.data.data || response.data;
      setForumPosts([newPostData, ...forumPosts]);
      setNewPost({ title: '', content: '' });
      alert('Post publié avec succès');
    } catch (err) {
      console.error('Erreur publication:', err);
      setError('Erreur lors de la publication du post');
    } finally {
      setLoading(false);
    }
  };

// ...existing code...
  // Fonction pour charger les attestations
  const fetchAttestations = async () => {
    setLoading(true);
    try {
      const response = await api.get('/attestations');
      setAttestations(response.data);
    } catch (err) {
      setError('Erreur lors du chargement des attestations');
    } finally {
      setLoading(false);
    }
  };

  // Fonction pour charger le profil étudiant
  const fetchStudentProfile = async () => {
    setLoading(true);
    try {
      const response = await api.get('/student/profile');
      setStudentProfile(response.data);
      setProfileForm({
        description: response.data.description || '',
        interests: response.data.interests || '',
        cv: null
      });
    } catch (err) {
      setError('Erreur lors du chargement du profil');
    } finally {
      setLoading(false);
    }
  };

  // Effet pour charger les données en fonction de la section active
  useEffect(() => {
    switch (activeSection) {
      case 'cours':
        fetchCourses();
        break;
      case 'evenements':
        fetchEvents();
        break;
      case 'forum':
        fetchForumPosts();
        break;
      case 'attestations':
        fetchAttestations();
        break;
      case 'profil':
        fetchStudentProfile();
        break;
      default:
        // Pour l'accueil, on charge les cours et événements pour les statistiques
        fetchCourses();
        fetchEvents();
        break;
    }
  }, [activeSection]);

  // Filtrer les cours en fonction de la recherche
  const filteredCourses = courses.filter(course =>
    course.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
    course.professor.toLowerCase().includes(searchTerm.toLowerCase())
  );

  // Gestion de la soumission d'une demande d'attestation
  const handleSubmitAttestation = async () => {
    if (!attestationRequest.reason) {
      alert('Veuillez indiquer une raison');
      return;
    }

    setLoading(true);
    try {
      const response = await api.post('/attestations', attestationRequest);
      setAttestations([...attestations, response.data]);
      setAttestationRequest({ type: 'scolaire', reason: '' });
      alert('Demande soumise avec succès');
    } catch (err) {
      setError('Erreur lors de la soumission de la demande');
    } finally {
      setLoading(false);
    }
  };

  // Gestion de la mise à jour du profil
  const handleUpdateProfile = async () => {
    setLoading(true);
    const formData = new FormData();
    formData.append('description', profileForm.description);
    formData.append('interests', profileForm.interests);
    if (profileForm.cv) {
      formData.append('cv', profileForm.cv);
    }

    try {
      const response = await api.post('/student/profile', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      setStudentProfile(response.data);
      alert('Profil mis à jour avec succès');
    } catch (err) {
      setError('Erreur lors de la mise à jour du profil');
    } finally {
      setLoading(false);
    }
  };

  // Téléchargement d'un cours
  const handleDownloadCourse = async (course) => {
    try {
      const response = await api.get(`/courses/${course.id}/download`, { responseType: 'blob' });
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', course.file_path.split('/').pop() || 'cours.pdf');
      document.body.appendChild(link);
      link.click();
    } catch (err) {
      setError('Erreur lors du téléchargement du cours');
    }
  };

  const renderContent = () => {
    switch (activeSection) {
      case 'cours':
        return (
          <div className="content-section">
            <div className="section-header">
              <h2>Mes Cours</h2>
              <div className="search-box">
                <input
                  type="text"
                  placeholder="Rechercher un cours..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                />
              </div>
            </div>
            {loading ? (
              <p>Chargement...</p>
            ) : (
              <div className="courses-grid">
                {filteredCourses.map(course => (
                  <div key={course.id} className="course-card">
                    <h3>{course.title}</h3>
                    <p>Professeur: {course.professor}</p>
                    <div className="course-actions">
                      <button className="btn btn-primary">Voir le cours</button>
                      <button className="btn btn-secondary" onClick={() => handleDownloadCourse(course)}>Télécharger</button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        );

      case 'forum':
        return (
          <div className="content-section">
            <h2>Forum de Discussion</h2>
            <div className="forum-container">
              <div className="new-post">
                <input
                  type="text"
                  placeholder="Titre de la question"
                  value={newPost.title}
                  onChange={(e) => setNewPost({...newPost, title: e.target.value})}
                />
                <textarea
                  placeholder="Posez votre question..."
                  rows="4"
                  value={newPost.content}
                  onChange={(e) => setNewPost({...newPost, content: e.target.value})}
                ></textarea>
                <button className="btn btn-primary" onClick={handleSubmitPost}>Publier</button>
              </div>
              <div className="posts-list">
                {forumPosts.map(post => (
                  <div key={post.id} className="post">
                    <h4>{post.title}</h4>
                    <p>{post.content}</p>
                    <span className="post-meta">Par {post.user?.name} - {new Date(post.created_at).toLocaleDateString()}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        );

      case 'evenements':
        return (
          <div className="content-section">
            <h2>Événements à Venir</h2>
            {loading ? (
              <p>Chargement...</p>
            ) : (
              <div className="events-list">
                {events.map(event => (
                  <div key={event.id} className="event-card">
                    <h4>{event.title}</h4>
                    <p>📅 {event.date}</p>
                    <p>📍 {event.location}</p>
                    <button className="btn btn-primary">Voir détails</button>
                  </div>
                ))}
              </div>
            )}
          </div>
        );

      case 'profil':
        return (
          <div className="content-section">
            <h2>Mon Profil</h2>
            {loading && studentProfile === null ? (
              <p>Chargement...</p>
            ) : (
              <div className="profile-info">
                <div className="profile-header">
                  <div className="avatar">{user.name.charAt(0)}</div>
                  <div>
                    <h3>{user.name}</h3>
                    <p>{user.email}</p>
                  </div>
                </div>
                <div className="profile-details">
                  <div className="detail-item">
                    <label>CV:</label>
                    <input
                      type="file"
                      accept=".pdf,.doc,.docx"
                      onChange={(e) => setProfileForm({...profileForm, cv: e.target.files[0]})}
                    />
                    {studentProfile?.cv && (
                      <a href={`http://localhost:8000/storage/${studentProfile.cv}`} target="_blank" rel="noopener noreferrer">Voir le CV actuel</a>
                    )}
                  </div>
                  <div className="detail-item">
                    <label>Description:</label>
                    <textarea
                      placeholder="Décrivez-vous..."
                      value={profileForm.description}
                      onChange={(e) => setProfileForm({...profileForm, description: e.target.value})}
                    ></textarea>
                  </div>
                  <div className="detail-item">
                    <label>Centres d'intérêt:</label>
                    <input
                      type="text"
                      placeholder="React, Node.js, IA..."
                      value={profileForm.interests}
                      onChange={(e) => setProfileForm({...profileForm, interests: e.target.value})}
                    />
                  </div>
                </div>
                <button className="btn btn-primary" onClick={handleUpdateProfile}>Sauvegarder</button>
              </div>
            )}
          </div>
        );

      case 'attestations':
        return (
          <div className="content-section">
            <h2>Demandes d'Attestation</h2>
            <div className="attestation-form">
              <select
                value={attestationRequest.type}
                onChange={(e) => setAttestationRequest({...attestationRequest, type: e.target.value})}
              >
                <option value="scolaire">Scolaire</option>
                <option value="stage">Stage</option>
                <option value="bourse">Bourse</option>
              </select>
              <textarea
                placeholder="Raison de la demande..."
                rows="4"
                value={attestationRequest.reason}
                onChange={(e) => setAttestationRequest({...attestationRequest, reason: e.target.value})}
              ></textarea>
              <button className="btn btn-primary" onClick={handleSubmitAttestation}>Soumettre la demande</button>
            </div>
            <div className="attestations-list">
              <h3>Mes demandes</h3>
              {attestations.map(att => (
                <div key={att.id} className="attestation-card">
                  <p><strong>Type:</strong> {att.type}</p>
                  <p><strong>Raison:</strong> {att.reason}</p>
                  <p><strong>Statut:</strong> {att.status}</p>
                </div>
              ))}
            </div>
          </div>
        );

      default:
        return (
          <div className="welcome-section">
            <h1>Bienvenue, {user.name} ! 🎓</h1>
            <p>Vous êtes connecté en tant qu'étudiant</p>
            
            <div className="stats-cards">
              <div className="stat-card">
                <h3>{courses.length}</h3>
                <p>Cours Disponibles</p>
              </div>
              <div className="stat-card">
                <h3>{events.length}</h3>
                <p>Événements à Venir</p>
              </div>
              <div className="stat-card">
                <h3>5</h3>
                <p>Messages Non Lus</p>
              </div>
            </div>

            <div className="quick-actions">
              <h3>Actions Rapides</h3>
              <div className="action-buttons">
                <button onClick={() => setActiveSection('cours')} className="btn btn-primary">
                  📚 Voir mes cours
                </button>
                <button onClick={() => setActiveSection('forum')} className="btn btn-secondary">
                  💬 Accéder au forum
                </button>
                <button onClick={() => setActiveSection('attestations')} className="btn btn-secondary">
                  📄 Demander une attestation
                </button>
              </div>
            </div>
          </div>
        );
    }
  };

  return (
    <div className="student-dashboard">
      {/* Sidebar */}
      <div className="sidebar">
        <div className="user-info">
          <div className="avatar">{user.name.charAt(0)}</div>
          <h3>{user.name}</h3>
          <p>Étudiant</p>
        </div>

        <nav className="nav-menu">
          <button 
            className={`nav-item ${activeSection === 'accueil' ? 'active' : ''}`}
            onClick={() => setActiveSection('accueil')}
          >
             Accueil
          </button>
          <button 
            className={`nav-item ${activeSection === 'cours' ? 'active' : ''}`}
            onClick={() => setActiveSection('cours')}
          >
             Mes Cours
          </button>
          <button 
            className={`nav-item ${activeSection === 'forum' ? 'active' : ''}`}
            onClick={() => setActiveSection('forum')}
          >
             Forum
          </button>
          <button 
            className={`nav-item ${activeSection === 'evenements' ? 'active' : ''}`}
            onClick={() => setActiveSection('evenements')}
          >
             Événements
          </button>
          <button 
            className={`nav-item ${activeSection === 'messages' ? 'active' : ''}`}
            onClick={() => setActiveSection('messages')}
          >
             Messages
          </button>
          <button 
            className={`nav-item ${activeSection === 'attestations' ? 'active' : ''}`}
            onClick={() => setActiveSection('attestations')}
          >
             Attestations
          </button>
          <button 
            className={`nav-item ${activeSection === 'profil' ? 'active' : ''}`}
            onClick={() => setActiveSection('profil')}
          >
          👤 Mon Profil
          </button>
          <button className="nav-item logout" onClick={onLogout}>
             Déconnexion
          </button>
        </nav>
      </div>

      {/* Contenu Principal */}
      <div className="main-content">
        <header className="header">
          <h1>Tableau de Bord Étudiant</h1>
          <div className="header-actions">
            <span>Bienvenue, {user.name}</span>
          </div>
        </header>

        <div className="content-area">
          {error && <div className="alert alert-danger">{error}</div>}
          {renderContent()}
        </div>
      </div>
    </div>
  );
};

export default StudentDashboard;