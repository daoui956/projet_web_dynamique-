// components/Dashboards/AdminDashboard.js
import React, { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import './AdminDashboard.css';

const AdminDashboard = () => {
  const { user, logout, admin } = useAuth();
  const [activeSection, setActiveSection] = useState('dashboard');
  const [stats, setStats] = useState({});
  const [users, setUsers] = useState([]);
  const [requests, setRequests] = useState([]); // Nouvelles demandes d'attestation
  const [loading, setLoading] = useState(false);
  
  // États pour les modals
  const [showUserModal, setShowUserModal] = useState(false);
  const [showResponseModal, setShowResponseModal] = useState(false);
  const [editingItem, setEditingItem] = useState(null);
  const [selectedRequest, setSelectedRequest] = useState(null);
  
  // États pour les formulaires
  const [userForm, setUserForm] = useState({
    name: '',
    email: '',
    type: 'student',
    password: '',
    active: true
  });
  
  const [responseForm, setResponseForm] = useState({
    status: 'approved',
    adminMessage: '',
    documentUrl: ''
  });

  // Charger les statistiques depuis l'API
  const loadStats = async () => {
    setLoading(true);
    try {
      const data = await admin.getStats();
      if (data.success) {
        setStats(data.data);
      } else {
        console.error('Erreur chargement stats:', data.message);
        setStats({
          totalUsers: 0,
          totalStudents: 0,
          totalTeachers: 0,
          totalAdmins: 0,
          pendingRequests: 0,
          approvedRequests: 0
        });
      }
    } catch (error) {
      console.error('Erreur chargement stats:', error);
      setStats({
        totalUsers: 0,
        totalStudents: 0,
        totalTeachers: 0,
        totalAdmins: 0,
        pendingRequests: 0,
        approvedRequests: 0
      });
    }
    setLoading(false);
  };

  // Charger les utilisateurs depuis l'API
  const loadUsers = async () => {
    setLoading(true);
    try {
      const data = await admin.getUsers();
      if (data.success) {
        setUsers(data.data);
      } else {
        console.error('Erreur chargement utilisateurs:', data.message);
        setUsers([]);
      }
    } catch (error) {
      console.error('Erreur chargement utilisateurs:', error);
      setUsers([]);
    }
    setLoading(false);
  };

  // Charger les demandes d'attestation depuis l'API
  const loadRequests = async () => {
    setLoading(true);
    try {
      const data = await admin.getRequests();
      if (data.success) {
        setRequests(data.data);
      } else {
        console.error('Erreur chargement demandes:', data.message);
        setRequests([]);
      }
    } catch (error) {
      console.error('Erreur chargement demandes:', error);
      setRequests([]);
    }
    setLoading(false);
  };

  useEffect(() => {
    loadStats();
  }, []);

  useEffect(() => {
    switch (activeSection) {
      case 'users':
        loadUsers();
        break;
      case 'requests':
        loadRequests();
        break;
      default:
        break;
    }
  }, [activeSection]);

  // Gestion des utilisateurs
  const handleAddUser = () => {
    setEditingItem(null);
    setUserForm({
      name: '',
      email: '',
      type: 'student',
      password: '',
      active: true
    });
    setShowUserModal(true);
  };

  const handleEditUser = (user) => {
    setEditingItem(user);
    setUserForm({
      name: user.name,
      email: user.email,
      type: user.type,
      password: '',
      active: user.active
    });
    setShowUserModal(true);
  };

  const handleSaveUser = async () => {
    try {
      let result;
      if (editingItem) {
        result = await admin.updateUser(editingItem.id, userForm);
        if (result.success) {
          await loadUsers();
          setShowUserModal(false);
        } else {
          alert('Erreur lors de la modification: ' + result.message);
        }
      } else {
        result = await admin.createUser(userForm);
        if (result.success) {
          await loadUsers();
          setShowUserModal(false);
        } else {
          alert('Erreur lors de la création: ' + result.message);
        }
      }
    } catch (error) {
      console.error('Erreur sauvegarde utilisateur:', error);
      alert('Erreur lors de la sauvegarde');
    }
  };

  const handleDeleteUser = async (userId) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
      try {
        const result = await admin.deleteUser(userId);
        if (result.success) {
          await loadUsers();
        } else {
          alert('Erreur lors de la suppression: ' + result.message);
        }
      } catch (error) {
        console.error('Erreur suppression utilisateur:', error);
        alert('Erreur lors de la suppression');
      }
    }
  };

  const handleToggleUserStatus = async (userId, currentStatus) => {
    try {
      const result = await admin.updateUser(userId, { active: !currentStatus });
      if (result.success) {
        await loadUsers();
      } else {
        alert('Erreur lors du changement de statut: ' + result.message);
      }
    } catch (error) {
      console.error('Erreur changement statut utilisateur:', error);
      alert('Erreur lors du changement de statut');
    }
  };

  // Gestion des demandes d'attestation
  const handleViewRequest = (request) => {
    setSelectedRequest(request);
    setResponseForm({
      status: request.status || 'pending',
      adminMessage: request.adminMessage || '',
      documentUrl: request.documentUrl || ''
    });
    setShowResponseModal(true);
  };

  const handleRespondToRequest = async () => {
    if (!selectedRequest) return;
    
    try {
      const result = await admin.updateRequest(selectedRequest.id, responseForm);
      if (result.success) {
        alert('Réponse envoyée avec succès!');
        await loadRequests();
        setShowResponseModal(false);
      } else {
        alert('Erreur lors de l\'envoi de la réponse: ' + result.message);
      }
    } catch (error) {
      console.error('Erreur réponse demande:', error);
      alert('Erreur lors de l\'envoi de la réponse');
    }
  };

  const handleDownloadDocument = async (requestId, documentType) => {
    try {
      const result = await admin.downloadRequestDocument(requestId, documentType);
      if (result.success && result.data.url) {
        window.open(result.data.url, '_blank');
      } else {
        alert('Document non disponible: ' + result.message);
      }
    } catch (error) {
      console.error('Erreur téléchargement document:', error);
      alert('Erreur lors du téléchargement');
    }
  };

  const getRequestTypeLabel = (type) => {
    switch (type) {
      case 'attestation-scolaire': return 'Attestation Scolaire';
      case 'attestation-stage': return 'Attestation de Stage';
      case 'attestation-bourse': return 'Attestation de Bourse';
      default: return type;
    }
  };

  const getStatusBadge = (status) => {
    switch (status) {
      case 'pending': return { class: 'pending', label: '⏳ En attente' };
      case 'approved': return { class: 'approved', label: '✅ Approuvé' };
      case 'rejected': return { class: 'rejected', label: '❌ Rejeté' };
      case 'processed': return { class: 'processed', label: '📄 Traité' };
      default: return { class: 'pending', label: status };
    }
  };

  const renderContent = () => {
    switch (activeSection) {
      case 'dashboard':
        return (
          <div className="admin-section">
            <h2>📊 Tableau de Bord</h2>
            <div className="stats-grid">
              <div className="stat-card">
                <div className="stat-icon">👥</div>
                <div className="stat-info">
                  <h3>{stats.totalUsers || 0}</h3>
                  <p>Utilisateurs Total</p>
                </div>
              </div>
              <div className="stat-card">
                <div className="stat-icon">🎓</div>
                <div className="stat-info">
                  <h3>{stats.totalStudents || 0}</h3>
                  <p>Étudiants</p>
                </div>
              </div>
              <div className="stat-card">
                <div className="stat-icon">👨‍🏫</div>
                <div className="stat-info">
                  <h3>{stats.totalTeachers || 0}</h3>
                  <p>Enseignants</p>
                </div>
              </div>
              <div className="stat-card">
                <div className="stat-icon">👤</div>
                <div className="stat-info">
                  <h3>{stats.totalAdmins || 0}</h3>
                  <p>Administrateurs</p>
                </div>
              </div>
              <div className="stat-card">
                <div className="stat-icon">📨</div>
                <div className="stat-info">
                  <h3>{stats.pendingRequests || 0}</h3>
                  <p>Demandes en attente</p>
                </div>
              </div>
              <div className="stat-card">
                <div className="stat-icon">✅</div>
                <div className="stat-info">
                  <h3>{stats.approvedRequests || 0}</h3>
                  <p>Demandes approuvées</p>
                </div>
              </div>
            </div>

            <div className="recent-activity">
              <h3>Activité Récente</h3>
              <div className="activity-list">
                <div className="activity-item">
                  <span className="activity-badge new">Nouveau</span>
                  <p>{stats.totalUsers || 0} utilisateurs inscrits</p>
                  <span className="activity-time">Mis à jour à l'instant</span>
                </div>
                <div className="activity-item">
                  <span className="activity-badge request">Demande</span>
                  <p>{stats.pendingRequests || 0} demandes en attente</p>
                  <span className="activity-time">Mis à jour à l'instant</span>
                </div>
                <div className="activity-item">
                  <span className="activity-badge approved">Traité</span>
                  <p>{stats.approvedRequests || 0} demandes traitées</p>
                  <span className="activity-time">Mis à jour à l'instant</span>
                </div>
              </div>
            </div>
          </div>
        );

      case 'users':
        return (
          <div className="admin-section">
            <div className="section-header">
              <h2>👥 Gestion des Utilisateurs</h2>
              <div className="header-actions">
                <button className="btn btn-primary" onClick={loadUsers}>
                  🔄 Actualiser
                </button>
                <button className="btn btn-success" onClick={handleAddUser}>
                  ➕ Ajouter Utilisateur
                </button>
              </div>
            </div>

            <div className="table-container">
              <table className="admin-table">
                <thead>
                  <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {users.length > 0 ? users.map(user => (
                    <tr key={user.id}>
                      <td>
                        <div className="user-info">
                          <div className="user-avatar">
                            {user.name?.charAt(0)}
                          </div>
                          <span>{user.name}</span>
                        </div>
                      </td>
                      <td>{user.email}</td>
                      <td>
                        <span className={`user-type ${user.type}`}>
                          {user.type === 'student' ? '🎓 Étudiant' : 
                           user.type === 'teacher' ? '👨‍🏫 Enseignant' :
                           '👤 Admin'}
                        </span>
                      </td>
                      <td>
                        <span className={`status-badge ${user.active ? 'active' : 'inactive'}`}>
                          {user.active ? 'Actif' : 'Inactif'}
                        </span>
                      </td>
                      <td>{new Date(user.created_at).toLocaleDateString('fr-FR')}</td>
                      <td>
                        <div className="action-buttons">
                          <button 
                            className="btn btn-sm btn-primary"
                            onClick={() => handleEditUser(user)}
                          >
                            ✏️ Modifier
                          </button>
                          <button 
                            className="btn btn-sm btn-secondary"
                            onClick={() => handleToggleUserStatus(user.id, user.active)}
                          >
                            {user.active ? '🚫 Désactiver' : '✅ Activer'}
                          </button>
                          <button 
                            className="btn btn-sm btn-danger"
                            onClick={() => handleDeleteUser(user.id)}
                          >
                            🗑️ Supprimer
                          </button>
                        </div>
                      </td>
                    </tr>
                  )) : (
                    <tr>
                      <td colSpan="6" className="no-data">
                        Aucun utilisateur trouvé
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        );

      case 'requests':
        return (
          <div className="admin-section">
            <div className="section-header">
              <h2>📨 Gestion des Demandes d'Attestation</h2>
              <div className="header-actions">
                <button className="btn btn-primary" onClick={loadRequests}>
                  🔄 Actualiser
                </button>
                <button className="btn btn-info" onClick={() => {
                  const types = ['attestation-scolaire', 'attestation-stage', 'attestation-bourse'];
                  const type = types[Math.floor(Math.random() * types.length)];
                  console.log('Type de demande:', type);
                }}>
                  📊 Statistiques
                </button>
              </div>
            </div>

            <div className="filters">
              <div className="filter-group">
                <label>Filtrer par type:</label>
                <select onChange={(e) => {
                  // Filtrage côté client ou appel API filtré
                  console.log('Filtrer par type:', e.target.value);
                }}>
                  <option value="">Tous les types</option>
                  <option value="attestation-scolaire">Attestation Scolaire</option>
                  <option value="attestation-stage">Attestation de Stage</option>
                  <option value="attestation-bourse">Attestation de Bourse</option>
                </select>
              </div>
              <div className="filter-group">
                <label>Filtrer par statut:</label>
                <select onChange={(e) => {
                  console.log('Filtrer par statut:', e.target.value);
                }}>
                  <option value="">Tous les statuts</option>
                  <option value="pending">En attente</option>
                  <option value="approved">Approuvé</option>
                  <option value="rejected">Rejeté</option>
                  <option value="processed">Traité</option>
                </select>
              </div>
            </div>

            <div className="table-container">
              <table className="admin-table">
                <thead>
                  <tr>
                    <th>Étudiant</th>
                    <th>Type de demande</th>
                    <th>Date de demande</th>
                    <th>Statut</th>
                    <th>Message de l'étudiant</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {requests.length > 0 ? requests.map(request => {
                    const statusInfo = getStatusBadge(request.status);
                    return (
                      <tr key={request.id}>
                        <td>
                          <div className="user-info">
                            <div className="user-avatar">
                              {request.student?.name?.charAt(0) || 'E'}
                            </div>
                            <div>
                              <strong>{request.student?.name || 'Étudiant'}</strong>
                              <small>{request.student?.email}</small>
                            </div>
                          </div>
                        </td>
                        <td>
                          <span className={`request-type ${request.type}`}>
                            {getRequestTypeLabel(request.type)}
                          </span>
                        </td>
                        <td>
                          {new Date(request.created_at).toLocaleDateString('fr-FR')}
                          <br />
                          <small>{new Date(request.created_at).toLocaleTimeString('fr-FR')}</small>
                        </td>
                        <td>
                          <span className={`status-badge ${statusInfo.class}`}>
                            {statusInfo.label}
                          </span>
                        </td>
                        <td className="message-cell">
                          <div className="message-preview">
                            {request.message ? (
                              <>
                                {request.message.substring(0, 80)}
                                {request.message.length > 80 ? '...' : ''}
                              </>
                            ) : (
                              <em>Aucun message</em>
                            )}
                          </div>
                          {request.files && request.files.length > 0 && (
                            <div className="files-indicator">
                              📎 {request.files.length} fichier(s)
                            </div>
                          )}
                        </td>
                        <td>
                          <div className="action-buttons">
                            <button 
                              className="btn btn-sm btn-primary"
                              onClick={() => handleViewRequest(request)}
                            >
                              👁️ Voir
                            </button>
                            {request.status === 'pending' && (
                              <>
                                <button 
                                  className="btn btn-sm btn-success"
                                  onClick={async () => {
                                    if (window.confirm('Approuver cette demande ?')) {
                                      const result = await admin.updateRequest(request.id, { status: 'approved' });
                                      if (result.success) await loadRequests();
                                    }
                                  }}
                                >
                                  ✅ Approuver
                                </button>
                                <button 
                                  className="btn btn-sm btn-danger"
                                  onClick={async () => {
                                    if (window.confirm('Rejeter cette demande ?')) {
                                      const result = await admin.updateRequest(request.id, { status: 'rejected' });
                                      if (result.success) await loadRequests();
                                    }
                                  }}
                                >
                                  ❌ Rejeter
                                </button>
                              </>
                            )}
                            {request.documentUrl && (
                              <button 
                                className="btn btn-sm btn-info"
                                onClick={() => window.open(request.documentUrl, '_blank')}
                              >
                                📄 Document
                              </button>
                            )}
                          </div>
                        </td>
                      </tr>
                    );
                  }) : (
                    <tr>
                      <td colSpan="6" className="no-data">
                        Aucune demande d'attestation trouvée
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        );

      case 'settings':
        return (
          <div className="admin-section">
            <h2>⚙️ Paramètres Administrateur</h2>
            <div className="settings-grid">
              <div className="setting-card">
                <h3>Paramètres Généraux</h3>
                <div className="setting-item">
                  <label>Notifications par email</label>
                  <input type="checkbox" defaultChecked />
                </div>
                <div className="setting-item">
                  <label>Notifications pour nouvelles demandes</label>
                  <input type="checkbox" defaultChecked />
                </div>
                <div className="setting-item">
                  <label>Délai de traitement (jours)</label>
                  <input type="number" defaultValue="3" min="1" max="30" />
                </div>
              </div>
              
              <div className="setting-card">
                <h3>Paramètres des Attestations</h3>
                <div className="setting-item">
                  <label>Format des documents</label>
                  <select defaultValue="pdf">
                    <option value="pdf">PDF</option>
                    <option value="docx">Word</option>
                    <option value="both">Les deux</option>
                  </select>
                </div>
                <div className="setting-item">
                  <label>Signature électronique</label>
                  <input type="checkbox" defaultChecked />
                </div>
                <div className="setting-item">
                  <label>Cachet de l'établissement</label>
                  <input type="checkbox" defaultChecked />
                </div>
              </div>

              <div className="setting-card">
                <h3>Apparence</h3>
                <div className="setting-item">
                  <label>Thème</label>
                  <select defaultValue="light">
                    <option value="light">Clair</option>
                    <option value="dark">Sombre</option>
                    <option value="auto">Automatique</option>
                  </select>
                </div>
                <div className="setting-item">
                  <label>Langue</label>
                  <select defaultValue="fr">
                    <option value="fr">Français</option>
                    <option value="en">English</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div className="settings-actions">
              <button className="btn btn-primary">Enregistrer les paramètres</button>
              <button className="btn btn-secondary">Réinitialiser</button>
            </div>
          </div>
        );

      default:
        return null;
    }
  };

  // Rendu des modals
  const renderModals = () => {
    return (
      <>
        {/* Modal Utilisateur */}
        {showUserModal && (
          <div className="modal-overlay">
            <div className="modal-content">
              <div className="modal-header">
                <h3>{editingItem ? 'Modifier Utilisateur' : 'Ajouter Utilisateur'}</h3>
                <button className="close-button" onClick={() => setShowUserModal(false)}>
                  ×
                </button>
              </div>
              
              <div className="modal-body">
                <div className="form-group">
                  <label>Nom</label>
                  <input
                    type="text"
                    value={userForm.name}
                    onChange={(e) => setUserForm({...userForm, name: e.target.value})}
                    placeholder="Nom complet"
                  />
                </div>
                
                <div className="form-group">
                  <label>Email</label>
                  <input
                    type="email"
                    value={userForm.email}
                    onChange={(e) => setUserForm({...userForm, email: e.target.value})}
                    placeholder="email@exemple.com"
                  />
                </div>
                
                <div className="form-group">
                  <label>Type</label>
                  <select
                    value={userForm.type}
                    onChange={(e) => setUserForm({...userForm, type: e.target.value})}
                  >
                    <option value="student">Étudiant</option>
                    <option value="teacher">Enseignant</option>
                    <option value="admin">Administrateur</option>
                  </select>
                </div>
                
                <div className="form-group">
                  <label>Mot de passe</label>
                  <input
                    type="password"
                    value={userForm.password}
                    onChange={(e) => setUserForm({...userForm, password: e.target.value})}
                    placeholder={editingItem ? "Laisser vide pour ne pas changer" : "Mot de passe"}
                  />
                </div>
                
                <div className="form-group">
                  <label>
                    <input
                      type="checkbox"
                      checked={userForm.active}
                      onChange={(e) => setUserForm({...userForm, active: e.target.checked})}
                    />
                    Compte actif
                  </label>
                </div>
              </div>
              
              <div className="modal-actions">
                <button className="btn btn-secondary" onClick={() => setShowUserModal(false)}>
                  Annuler
                </button>
                <button className="btn btn-primary" onClick={handleSaveUser}>
                  {editingItem ? 'Modifier' : 'Ajouter'}
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Modal Réponse à la demande d'attestation */}
        {showResponseModal && selectedRequest && (
          <div className="modal-overlay">
            <div className="modal-content">
              <div className="modal-header">
                <h3>Répondre à la demande d'attestation</h3>
                <button className="close-button" onClick={() => setShowResponseModal(false)}>
                  ×
                </button>
              </div>
              
              <div className="modal-body">
                <div className="request-info">
                  <h4>Informations de la demande</h4>
                  <p><strong>Étudiant:</strong> {selectedRequest.student?.name}</p>
                  <p><strong>Type:</strong> {getRequestTypeLabel(selectedRequest.type)}</p>
                  <p><strong>Date:</strong> {new Date(selectedRequest.created_at).toLocaleDateString('fr-FR')}</p>
                  
                  {selectedRequest.message && (
                    <div className="student-message">
                      <strong>Message de l'étudiant:</strong>
                      <p>{selectedRequest.message}</p>
                    </div>
                  )}
                </div>
                
                <div className="form-group">
                  <label>Statut</label>
                  <select
                    value={responseForm.status}
                    onChange={(e) => setResponseForm({...responseForm, status: e.target.value})}
                  >
                    <option value="pending">⏳ En attente</option>
                    <option value="approved">✅ Approuvé</option>
                    <option value="rejected">❌ Rejeté</option>
                    <option value="processed">📄 Traité</option>
                  </select>
                </div>
                
                <div className="form-group">
                  <label>Message de l'administrateur (optionnel)</label>
                  <textarea
                    value={responseForm.adminMessage}
                    onChange={(e) => setResponseForm({...responseForm, adminMessage: e.target.value})}
                    placeholder="Ex: Votre attestation est prête. Vous pouvez la récupérer..."
                    rows="4"
                  />
                </div>
                
                <div className="form-group">
                  <label>URL du document (optionnel)</label>
                  <input
                    type="text"
                    value={responseForm.documentUrl}
                    onChange={(e) => setResponseForm({...responseForm, documentUrl: e.target.value})}
                    placeholder="https://drive.google.com/..."
                  />
                </div>
                
                {selectedRequest.files && selectedRequest.files.length > 0 && (
                  <div className="request-files">
                    <strong>Fichiers joints par l'étudiant:</strong>
                    <ul>
                      {selectedRequest.files.map((file, index) => (
                        <li key={index}>
                          <a href={file.url} target="_blank" rel="noopener noreferrer">
                            📎 {file.name || `Fichier ${index + 1}`}
                          </a>
                        </li>
                      ))}
                    </ul>
                  </div>
                )}
              </div>
              
              <div className="modal-actions">
                <button className="btn btn-secondary" onClick={() => setShowResponseModal(false)}>
                  Annuler
                </button>
                <button className="btn btn-primary" onClick={handleRespondToRequest}>
                  Envoyer la réponse
                </button>
                <button 
                  className="btn btn-success"
                  onClick={() => {
                    // Générer automatiquement l'attestation
                    alert('Fonctionnalité de génération d\'attestation à implémenter');
                  }}
                >
                  📄 Générer l'attestation
                </button>
              </div>
            </div>
          </div>
        )}
      </>
    );
  };

  return (
    <div className="admin-dashboard">
      {/* Sidebar */}
      <div className="admin-sidebar">
        <div className="sidebar-header">
          <div className="admin-avatar">
            {user?.name?.charAt(0) || 'A'}
          </div>
          <div className="admin-info">
            <h3>{user?.name || 'Administrateur'}</h3>
            <p>Administrateur Système</p>
          </div>
        </div>

        <nav className="admin-nav">
          <button 
            className={`nav-item ${activeSection === 'dashboard' ? 'active' : ''}`}
            onClick={() => setActiveSection('dashboard')}
          >
            📊 Tableau de Bord
          </button>
          <button 
            className={`nav-item ${activeSection === 'users' ? 'active' : ''}`}
            onClick={() => setActiveSection('users')}
          >
            👥 Utilisateurs
          </button>
          <button 
            className={`nav-item ${activeSection === 'requests' ? 'active' : ''}`}
            onClick={() => setActiveSection('requests')}
          >
            📨 Demandes d'Attestation
          </button>
          <button 
            className={`nav-item ${activeSection === 'settings' ? 'active' : ''}`}
            onClick={() => setActiveSection('settings')}
          >
            ⚙️ Paramètres
          </button>
        </nav>

        <div className="sidebar-footer">
          <button className="logout-btn" onClick={logout}>
            🚪 Déconnexion
          </button>
        </div>
      </div>

      {/* Main Content */}
      <div className="admin-main">
        <header className="admin-header">
          <h1>Administration - Gestion des Attestations</h1>
          <div className="header-actions">
            <span className="welcome">Bonjour, {user?.name}</span>
            <span className="notification-badge">
              🔔 {stats.pendingRequests || 0}
            </span>
          </div>
        </header>

        <main className="admin-content">
          {loading ? (
            <div className="loading-admin">
              <div className="spinner"></div>
              <p>Chargement des données...</p>
            </div>
          ) : (
            renderContent()
          )}
        </main>
      </div>

      {/* Modals */}
      {renderModals()}
    </div>
  );
};

export default AdminDashboard;