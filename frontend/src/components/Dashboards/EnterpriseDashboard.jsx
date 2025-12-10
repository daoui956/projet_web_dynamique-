import React, { useState, useEffect } from "react";
import axios from "axios";
import './EnterpriseDashboard.css';

const EnterpriseDashboard = ({ user, onLogout }) => {
  const [activeSection, setActiveSection] = useState('accueil');
  const [showForm, setShowForm] = useState(false);
  const [titre, setTitre] = useState("");
  const [description, setDescription] = useState("");
  const [etudiants, setEtudiants] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedStudent, setSelectedStudent] = useState(null);
  const [showStudentModal, setShowStudentModal] = useState(false);
  
  // États pour la section contact
  const [contacts, setContacts] = useState([]);
  const [newMessage, setNewMessage] = useState("");
  const [selectedContact, setSelectedContact] = useState(null);
  
  // États pour la section profil
  const [entrepriseInfo, setEntrepriseInfo] = useState({
    nom: "",
    email: "",
    telephone: "",
    adresse: "",
    secteur: "",
    description: "",
    site_web: ""
  });
  const [editingProfil, setEditingProfil] = useState(false);

  // Charger les étudiants
  const chargerEtudiants = async () => {
    try {
      setLoading(true);
      const response = await axios.get(
        "http://localhost:8000/api/entreprise/cvs",
        { 
          headers: { 
            Authorization: `Bearer ${user.token}`,
            'Accept': 'application/json'
          } 
        }
      );
      
      if (response.data.success) {
        setEtudiants(response.data.data);
      } else {
        setError("Erreur lors du chargement des CV");
      }
    } catch (err) {
      console.error('Erreur chargement étudiants:', err);
      setError("Erreur de connexion au serveur");
    } finally {
      setLoading(false);
    }
  };

  // Charger les contacts
  const chargerContacts = async () => {
    try {
      const response = await axios.get(
        "http://localhost:8000/api/entreprise/contacts",
        {
          headers: {
            Authorization: `Bearer ${user.token}`,
            'Accept': 'application/json'
          }
        }
      );
      
      if (response.data.success) {
        setContacts(response.data.data);
      } else {
        // Simulation de données si l'API n'est pas disponible
        const contactsSimules = [
          {
            id: 1,
            nom: "Jean Dupont",
            email: "jean.dupont@email.com",
            sujet: "Candidature Stage Développement",
            message: "Bonjour, je suis intéressé par votre offre de stage...",
            date: "2024-01-15",
            lu: false
          }
        ];
        setContacts(contactsSimules);
      }
    } catch (err) {
      console.error('Erreur chargement contacts:', err);
      // Fallback à des données simulées
      const contactsSimules = [
        {
          id: 1,
          nom: "Jean Dupont",
          email: "jean.dupont@email.com",
          sujet: "Candidature Stage Développement",
          message: "Bonjour, je suis intéressé par votre offre de stage...",
          date: "2024-01-15",
          lu: false
        }
      ];
      setContacts(contactsSimules);
    }
  };

  // Charger le profil entreprise
  const chargerProfilEntreprise = async () => {
    try {
      const response = await axios.get(
        "http://localhost:8000/api/entreprise/profil",
        {
          headers: {
            Authorization: `Bearer ${user.token}`,
            'Accept': 'application/json'
          }
        }
      );
      
      if (response.data.success) {
        setEntrepriseInfo({
          nom: response.data.data.name || user?.name,
          email: response.data.data.email || user?.email,
          telephone: response.data.data.telephone || "",
          adresse: response.data.data.adresse || "",
          secteur: response.data.data.secteur_activite || "",
          description: response.data.data.description || "",
          site_web: response.data.data.site_web || ""
        });
      } else {
        // Données par défaut
        const profilSimule = {
          nom: user?.name || "Nom de l'entreprise",
          email: user?.email || "contact@entreprise.com",
          telephone: "+33 1 23 45 67 89",
          adresse: "123 Avenue des Champs-Élysées, 75008 Paris",
          secteur: "Technologie / Développement Web",
          description: "Entreprise spécialisée dans le développement de solutions web innovantes.",
          site_web: "www.entreprise-exemple.com"
        };
        setEntrepriseInfo(profilSimule);
      }
    } catch (err) {
      console.error('Erreur chargement profil:', err);
      // Données par défaut en cas d'erreur
      const profilSimule = {
        nom: user?.name || "Nom de l'entreprise",
        email: user?.email || "contact@entreprise.com",
        telephone: "+33 1 23 45 67 89",
        adresse: "123 Avenue des Champs-Élysées, 75008 Paris",
        secteur: "Technologie / Développement Web",
        description: "Entreprise spécialisée dans le développement de solutions web innovantes.",
        site_web: "www.entreprise-exemple.com"
      };
      setEntrepriseInfo(profilSimule);
    }
  };

  // Sauvegarder le profil entreprise
  const sauvegarderProfil = async () => {
    try {
      const response = await axios.put(
        "http://localhost:8000/api/entreprise/profil",
        {
          name: entrepriseInfo.nom,
          email: entrepriseInfo.email,
          telephone: entrepriseInfo.telephone,
          adresse: entrepriseInfo.adresse,
          site_web: entrepriseInfo.site_web,
          description: entrepriseInfo.description,
          secteur_activite: entrepriseInfo.secteur
        },
        {
          headers: {
            Authorization: `Bearer ${user.token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        }
      );
      
      if (response.data.success) {
        setEditingProfil(false);
        alert("Profil mis à jour avec succès !");
      } else {
        alert("Erreur: " + response.data.message);
      }
    } catch (err) {
      console.error('Erreur sauvegarde profil:', err);
      alert("Erreur lors de la mise à jour du profil");
    }
  };

  // Rechercher des étudiants
  const rechercherEtudiants = async (competence) => {
    try {
      setLoading(true);
      const response = await axios.get(
        `http://localhost:8000/api/entreprise/rechercher-etudiants?competence=${competence}`,
        { 
          headers: { 
            Authorization: `Bearer ${user.token}`,
            'Accept': 'application/json'
          } 
        }
      );
      
      if (response.data.success) {
        setEtudiants(response.data.data);
      }
    } catch (err) {
      console.error('Erreur recherche:', err);
    } finally {
      setLoading(false);
    }
  };

  // Envoyer un message
  const envoyerMessage = async (destinataire, message) => {
    try {
      // Simulation d'envoi de message
      console.log("Message envoyé à:", destinataire, "Contenu:", message);
      alert("Message envoyé avec succès !");
      setNewMessage("");
    } catch (err) {
      console.error('Erreur envoi message:', err);
      alert("Erreur lors de l'envoi du message");
    }
  };

  useEffect(() => {
    if (activeSection === 'consulter-cv') {
      chargerEtudiants();
    } else if (activeSection === 'contacts') {
      chargerContacts();
    } else if (activeSection === 'profil') {
      chargerProfilEntreprise();
    }
  }, [activeSection]);

// Dans EnterpriseDashboard.js, remplacez la fonction handlePublierOffre par :

const handlePublierOffre = async () => {
  try {
    if (!titre.trim()) {
      alert("Veuillez saisir un titre pour l'offre");
      return;
    }
    
    if (!description.trim()) {
      alert("Veuillez saisir une description pour l'offre");
      return;
    }

    setLoading(true);
    
    const response = await axios.post(
      "http://localhost:8000/api/entreprise/offres",
      { 
        titre, 
        description,
        type: 'stage', // ou récupérer depuis un select
        lieu: '', // ajouter les champs manquants
        duree: '',
        remuneration: '',
        competences_requises: ''
      },
      { 
        headers: { 
          Authorization: `Bearer ${user.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        } 
      }
    );
    
    if (response.data.success) {
      alert(response.data.message);
      setTitre("");
      setDescription("");
      setShowForm(false);
      
      // Recharger la liste des offres
      if (activeSection === 'mes-offres') {
        // Ajoutez une fonction pour recharger les offres
      }
    } else {
      alert("Erreur: " + (response.data.message || "Inconnue"));
    }
  } catch (err) {
    console.error("Erreur détaillée:", err.response || err);
    
    if (err.response?.status === 422) {
      // Erreurs de validation
      const errors = err.response.data.errors;
      let errorMessage = "Erreurs de validation:\n";
      Object.keys(errors).forEach(key => {
        errorMessage += `- ${errors[key].join(', ')}\n`;
      });
      alert(errorMessage);
    } else if (err.response?.data?.message) {
      alert("Erreur: " + err.response.data.message);
    } else if (err.request) {
      alert("Erreur de connexion au serveur. Vérifiez que le serveur Laravel est en cours d'exécution.");
    } else {
      alert("Erreur: " + err.message);
    }
  } finally {
    setLoading(false);
  }
};

  const ouvrirProfilEtudiant = (etudiant) => {
    setSelectedStudent(etudiant);
    setShowStudentModal(true);
  };

  const filtrerEtudiants = etudiants.filter(etudiant =>
    etudiant.name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    etudiant.email?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const renderContent = () => {
    switch (activeSection) {
      case 'publier-offres':
        return (
          <div className="content-section">
            <h2>Publier une Offre de Stage</h2>
            <p>Créez et publiez de nouvelles offres de stage pour les étudiants.</p>
            <button 
              className="btn btn-primary" 
              onClick={() => setShowForm(true)}
              style={{marginTop: '20px'}}
            >
              📄 Nouvelle Offre de Stage
            </button>
          </div>
        );
      
      case 'consulter-cv':
        return (
          <div className="content-section">
            <div className="section-header">
              <h2>👨‍🎓 Consulter les CV Étudiants</h2>
              <div className="search-box">
                <input
                  type="text"
                  placeholder="Rechercher un étudiant..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                />
              </div>
            </div>

            {error && (
              <div className="error-message">
                ⚠️ {error}
              </div>
            )}

            <div className="quick-actions">
              <button 
                className="quick-action-btn"
                onClick={() => rechercherEtudiants('javascript')}
              >
                🔍 Développeurs JavaScript
              </button>
              <button 
                className="quick-action-btn"
                onClick={() => rechercherEtudiants('php')}
              >
                🔍 Développeurs PHP
              </button>
              <button 
                className="quick-action-btn"
                onClick={() => rechercherEtudiants('design')}
              >
                🔍 Designers
              </button>
            </div>

            {loading ? (
              <div className="loading">Chargement des profils étudiants...</div>
            ) : filtrerEtudiants.length === 0 ? (
              <div className="empty-state">
                <h3>📭 Aucun étudiant trouvé</h3>
                <p>Aucun profil étudiant ne correspond à votre recherche.</p>
              </div>
            ) : (
              <div className="students-grid">
                {filtrerEtudiants.map(etudiant => (
                  <div key={etudiant.id} className="student-card">
                    <div className="student-header">
                      <div className="student-avatar">
                        {etudiant.photo ? (
                          <img src={etudiant.photo} alt={etudiant.name} />
                        ) : (
                          <span>{etudiant.name?.charAt(0)}</span>
                        )}
                      </div>
                      <div className="student-info">
                        <h3>{etudiant.name}</h3>
                        <p>{etudiant.email}</p>
                        <span className="student-badge">Étudiant</span>
                      </div>
                    </div>
                    
                    <div className="student-details">
                      <div className="detail-item">
                        <span>📧</span>
                        <span>{etudiant.email}</span>
                      </div>
                      {etudiant.telephone && (
                        <div className="detail-item">
                          <span>📞</span>
                          <span>{etudiant.telephone}</span>
                        </div>
                      )}
                      <div className="detail-item">
                        <span>📅</span>
                        <span>Membre depuis {new Date(etudiant.created_at).toLocaleDateString('fr-FR')}</span>
                      </div>
                    </div>

                    <div className="student-actions">
                      <button 
                        className="btn btn-primary"
                        onClick={() => ouvrirProfilEtudiant(etudiant)}
                      >
                        👁️ Voir le profil
                      </button>
                      <button 
                        className="btn btn-secondary"
                        onClick={() => {
                          setSelectedContact({
                            nom: etudiant.name,
                            email: etudiant.email
                          });
                          setActiveSection('contacts');
                        }}
                      >
                        💌 Contacter
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        );

      case 'contacts':
        return (
          <div className="content-section">
            <div className="contacts-header">
              <h2>📬 Messagerie</h2>
              <p>Gérez vos communications avec les étudiants</p>
            </div>

            <div className="contacts-layout">
              <div className="contacts-list">
                <h3>Messages reçus ({contacts.length})</h3>
                <div className="messages-container">
                  {contacts.map(contact => (
                    <div 
                      key={contact.id} 
                      className={`message-item ${!contact.lu ? 'non-lu' : ''} ${selectedContact?.id === contact.id ? 'active' : ''}`}
                      onClick={() => setSelectedContact(contact)}
                    >
                      <div className="message-header">
                        <strong>{contact.nom}</strong>
                        <span className="message-date">{contact.date}</span>
                      </div>
                      <div className="message-preview">
                        <strong>{contact.sujet}</strong>
                        <p>{contact.message.substring(0, 60)}...</p>
                      </div>
                      {!contact.lu && <span className="new-badge">Nouveau</span>}
                    </div>
                  ))}
                </div>
              </div>

              <div className="contact-details">
                {selectedContact ? (
                  <>
                    <div className="contact-header">
                      <h3>{selectedContact.sujet}</h3>
                      <div className="contact-info">
                        <span><strong>De:</strong> {selectedContact.nom}</span>
                        <span><strong>Email:</strong> {selectedContact.email}</span>
                        <span><strong>Date:</strong> {selectedContact.date}</span>
                      </div>
                    </div>
                    
                    <div className="message-content">
                      <p>{selectedContact.message}</p>
                    </div>

                    <div className="reply-section">
                      <h4>Répondre</h4>
                      <textarea
                        className="reply-textarea"
                        placeholder="Tapez votre réponse ici..."
                        value={newMessage}
                        onChange={(e) => setNewMessage(e.target.value)}
                        rows="4"
                      />
                      <button 
                        className="btn btn-primary"
                        onClick={() => envoyerMessage(selectedContact.email, newMessage)}
                        disabled={!newMessage.trim()}
                      >
                        📤 Envoyer la réponse
                      </button>
                    </div>
                  </>
                ) : (
                  <div className="no-selection">
                    <h3>👈 Sélectionnez un message</h3>
                    <p>Choisissez un message dans la liste pour lire et répondre</p>
                  </div>
                )}
              </div>
            </div>
          </div>
        );

      case 'profil':
        return (
          <div className="content-section">
            <div className="profil-header">
              <h2>🏢 Profil de l'Entreprise</h2>
              <button 
                className={`btn ${editingProfil ? 'btn-secondary' : 'btn-primary'}`}
                onClick={() => setEditingProfil(!editingProfil)}
              >
                {editingProfil ? '✖️ Annuler' : '✏️ Modifier le profil'}
              </button>
            </div>

            <div className="profil-form">
              <div className="form-grid">
                <div className="form-group">
                  <label>Nom de l'entreprise</label>
                  <input
                    type="text"
                    className="form-input"
                    value={entrepriseInfo.nom}
                    onChange={(e) => setEntrepriseInfo({...entrepriseInfo, nom: e.target.value})}
                    disabled={!editingProfil}
                  />
                </div>

                <div className="form-group">
                  <label>Email</label>
                  <input
                    type="email"
                    className="form-input"
                    value={entrepriseInfo.email}
                    onChange={(e) => setEntrepriseInfo({...entrepriseInfo, email: e.target.value})}
                    disabled={!editingProfil}
                  />
                </div>

                <div className="form-group">
                  <label>Téléphone</label>
                  <input
                    type="tel"
                    className="form-input"
                    value={entrepriseInfo.telephone}
                    onChange={(e) => setEntrepriseInfo({...entrepriseInfo, telephone: e.target.value})}
                    disabled={!editingProfil}
                  />
                </div>

                <div className="form-group">
                  <label>Site web</label>
                  <input
                    type="url"
                    className="form-input"
                    value={entrepriseInfo.site_web}
                    onChange={(e) => setEntrepriseInfo({...entrepriseInfo, site_web: e.target.value})}
                    disabled={!editingProfil}
                  />
                </div>

                <div className="form-group full-width">
                  <label>Adresse</label>
                  <input
                    type="text"
                    className="form-input"
                    value={entrepriseInfo.adresse}
                    onChange={(e) => setEntrepriseInfo({...entrepriseInfo, adresse: e.target.value})}
                    disabled={!editingProfil}
                  />
                </div>

                <div className="form-group full-width">
                  <label>Secteur d'activité</label>
                  <input
                    type="text"
                    className="form-input"
                    value={entrepriseInfo.secteur}
                    onChange={(e) => setEntrepriseInfo({...entrepriseInfo, secteur: e.target.value})}
                    disabled={!editingProfil}
                  />
                </div>

                <div className="form-group full-width">
                  <label>Description de l'entreprise</label>
                  <textarea
                    className="form-textarea"
                    rows="4"
                    value={entrepriseInfo.description}
                    onChange={(e) => setEntrepriseInfo({...entrepriseInfo, description: e.target.value})}
                    disabled={!editingProfil}
                  />
                </div>
              </div>

              {editingProfil && (
                <div className="form-actions">
                  <button 
                    className="btn btn-primary"
                    onClick={sauvegarderProfil}
                  >
                    💾 Sauvegarder les modifications
                  </button>
                </div>
              )}
            </div>

            <div className="profil-stats">
              <h3>📊 Statistiques</h3>
              <div className="stats-grid">
                <div className="stat-card">
                  <span className="stat-number">{etudiants.length}</span>
                  <span className="stat-label">Étudiants visibles</span>
                </div>
                <div className="stat-card">
                  <span className="stat-number">{contacts.length}</span>
                  <span className="stat-label">Messages reçus</span>
                </div>
                <div className="stat-card">
                  <span className="stat-number">3</span>
                  <span className="stat-label">Offres publiées</span>
                </div>
              </div>
            </div>
          </div>
        );

      default:
        return (
          <div className="welcome-section">
            <h1>Bienvenue, {user?.name} ! 🏢</h1>
            <p>Vous êtes connecté en tant qu'entreprise</p>
            
            <div className="dashboard-grid">
              <div className="dashboard-card" onClick={() => setShowForm(true)}>
                <span className="dashboard-card-icon">📄</span>
                <h3>Publier Offres de Stage</h3>
                <p>Créez et publiez de nouvelles offres de stage</p>
              </div>
              
              <div className="dashboard-card" onClick={() => setActiveSection('consulter-cv')}>
                <span className="dashboard-card-icon">👨‍🎓</span>
                <h3>Consulter CV Étudiants</h3>
                <p>Accédez aux profils des étudiants</p>
              </div>
              
              <div className="dashboard-card" onClick={() => setActiveSection('contacts')}>
                <span className="dashboard-card-icon">📬</span>
                <h3>Contacts</h3>
                <p>Gérez vos communications</p>
              </div>
              
              <div className="dashboard-card" onClick={() => setActiveSection('profil')}>
                <span className="dashboard-card-icon">🏢</span>
                <h3>Gérer Profil</h3>
                <p>Modifiez vos informations entreprise</p>
              </div>
            </div>

            <div className="recent-activity">
              <h3>🕐 Activité récente</h3>
              <div className="activity-list">
                <div className="activity-item">
                  <span className="activity-icon">📨</span>
                  <div className="activity-content">
                    <p>Nouveau message de Jean Dupont</p>
                    <span className="activity-time">Il y a 2 heures</span>
                  </div>
                </div>
                <div className="activity-item">
                  <span className="activity-icon">👁️</span>
                  <div className="activity-content">
                    <p>Votre offre a été consultée 15 fois</p>
                    <span className="activity-time">Aujourd'hui</span>
                  </div>
                </div>
                <div className="activity-item">
                  <span className="activity-icon">📄</span>
                  <div className="activity-content">
                    <p>Offre "Développeur Frontend" publiée</p>
                    <span className="activity-time">Hier</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        );
    }
  };

  return (
    <div className="enterprise-dashboard">
      <div className="sidebar">
        <div className="user-info">
          <div className="avatar">{user?.name?.charAt(0)}</div>
          <h3>{user?.name}</h3>
          <p>Entreprise</p>
        </div>

        <nav className="nav-menu">
          <button 
            className={activeSection === 'accueil' ? 'active' : ''}
            onClick={() => setActiveSection('accueil')}
          >
            🏠 Accueil
          </button>
          <button 
            className={activeSection === 'publier-offres' ? 'active' : ''}
            onClick={() => setActiveSection('publier-offres')}
          >
            📄 Publier Offres
          </button>
          <button 
            className={activeSection === 'consulter-cv' ? 'active' : ''}
            onClick={() => setActiveSection('consulter-cv')}
          >
            👨‍🎓 CV Étudiants
          </button>
          <button 
            className={activeSection === 'contacts' ? 'active' : ''}
            onClick={() => setActiveSection('contacts')}
          >
            📬 Contacts
          </button>
          <button 
            className={activeSection === 'profil' ? 'active' : ''}
            onClick={() => setActiveSection('profil')}
          >
            🏢 Profil Entreprise
          </button>
          <button className="logout" onClick={onLogout}>
            🚪 Déconnexion
          </button>
        </nav>
      </div>

      <div className="main-content">
        <header className="header">
          <h1>Espace Entreprise</h1>
          <div className="header-actions">
            <span className="welcome-text">Bonjour, {user?.name}</span>
          </div>
        </header>
        <div className="content-area">
          {renderContent()}
        </div>
      </div>

      {/* Modal Publication Offre */}
      {showForm && (
        <div className="modal-overlay">
          <div className="modal-content">
            <div className="modal-header">
              <h3>Ajouter une offre de stage</h3>
              <button className="close-button" onClick={() => setShowForm(false)}>
                ×
              </button>
            </div>
            
            <div className="form-group">
              <label>Titre de l'offre</label>
              <input
                type="text"
                className="form-input"
                placeholder="Ex: Développeur Web Frontend"
                value={titre}
                onChange={(e) => setTitre(e.target.value)}
              />
            </div>
            
            <div className="form-group">
              <label>Description de l'offre</label>
              <textarea
                className="form-textarea"
                placeholder="Décrivez les missions, compétences requises, durée du stage..."
                value={description}
                onChange={(e) => setDescription(e.target.value)}
              />
            </div>
            
            <div className="form-actions">
              <button className="btn btn-secondary" onClick={() => setShowForm(false)}>
                Annuler
              </button>
              <button className="btn btn-primary" onClick={handlePublierOffre}>
                Publier l'offre
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Modal Profil Étudiant */}
      {showStudentModal && selectedStudent && (
        <div className="modal-overlay">
          <div className="modal-content large-modal">
            <div className="modal-header">
              <h3>Profil de {selectedStudent.name}</h3>
              <button className="close-button" onClick={() => setShowStudentModal(false)}>
                ×
              </button>
            </div>
            
            <div className="student-profile">
              <div className="profile-header">
                <div className="profile-avatar">
                  {selectedStudent.photo ? (
                    <img src={selectedStudent.photo} alt={selectedStudent.name} />
                  ) : (
                    <span>{selectedStudent.name?.charAt(0)}</span>
                  )}
                </div>
                <div className="profile-info">
                  <h2>{selectedStudent.name}</h2>
                  <p>{selectedStudent.email}</p>
                  <span className="profile-badge">Étudiant</span>
                </div>
              </div>

              <div className="profile-sections">
                <div className="profile-section">
                  <h4>📧 Contact</h4>
                  <div className="contact-info">
                    <p><strong>Email:</strong> {selectedStudent.email}</p>
                    {selectedStudent.telephone && (
                      <p><strong>Téléphone:</strong> {selectedStudent.telephone}</p>
                    )}
                    {selectedStudent.adresse && (
                      <p><strong>Adresse:</strong> {selectedStudent.adresse}</p>
                    )}
                  </div>
                </div>

                <div className="profile-section">
                  <h4>📚 Informations académiques</h4>
                  <p>Membre depuis {new Date(selectedStudent.created_at).toLocaleDateString('fr-FR')}</p>
                </div>

                <div className="profile-section">
                  <h4>💼 Compétences</h4>
                  <div className="competences-list">
                    <span className="competence-tag">Développement Web</span>
                    <span className="competence-tag">JavaScript</span>
                    <span className="competence-tag">React</span>
                    <span className="competence-tag">PHP</span>
                  </div>
                </div>
              </div>

              <div className="profile-actions">
                <button className="btn btn-primary">
                  📄 Télécharger le CV
                </button>
                <button 
                  className="btn btn-secondary"
                  onClick={() => {
                    setShowStudentModal(false);
                    setSelectedContact({
                      nom: selectedStudent.name,
                      email: selectedStudent.email
                    });
                    setActiveSection('contacts');
                  }}
                >
                  💌 Envoyer un message
                </button>
                <button className="btn btn-success">
                  🤝 Proposer un stage
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default EnterpriseDashboard;