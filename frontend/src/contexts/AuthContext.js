// contexts/AuthContext.js
import React, { createContext, useState, useContext, useEffect } from 'react';

const AuthContext = createContext();

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [token, setToken] = useState(localStorage.getItem('token'));

  // Configuration de base pour les appels API
  const apiConfig = {
    baseURL: 'http://localhost:8000/api', // Adaptez selon votre URL Laravel
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    }
  };

  const getAuthHeaders = () => {
    const headers = { ...apiConfig.headers };
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }
    return headers;
  };

  // Fonctions d'authentification
  const login = async (email, password) => {
    try {
      const response = await fetch(`${apiConfig.baseURL}/login`, {
        method: 'POST',
        headers: apiConfig.headers,
        body: JSON.stringify({ email, password })
      });

      const data = await response.json();

      if (data.success) {
        setUser(data.user);
        setToken(data.token);
        localStorage.setItem('token', data.token);
        return { success: true, user: data.user };
      } else {
        return { success: false, message: data.message };
      }
    } catch (error) {
      console.error('Login error:', error);
      return { success: false, message: 'Erreur de connexion au serveur' };
    }
  };

  const register = async (userData) => {
    try {
      const response = await fetch(`${apiConfig.baseURL}/register`, {
        method: 'POST',
        headers: apiConfig.headers,
        body: JSON.stringify(userData)
      });

      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Register error:', error);
      return { success: false, message: 'Erreur d\'inscription' };
    }
  };

  const logout = async () => {
    try {
      if (token) {
        await fetch(`${apiConfig.baseURL}/logout`, {
          method: 'POST',
          headers: getAuthHeaders()
        });
      }
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      setUser(null);
      setToken(null);
      localStorage.removeItem('token');
    }
  };

  const fetchUser = async () => {
    if (!token) {
      setLoading(false);
      return;
    }

    try {
      const response = await fetch(`${apiConfig.baseURL}/user`, {
        headers: getAuthHeaders()
      });

      const data = await response.json();

      if (data.success) {
        setUser(data.user);
      } else {
        console.error('Fetch user failed:', data.message);
        localStorage.removeItem('token');
        setToken(null);
      }
    } catch (error) {
      console.error('Fetch user error:', error);
      localStorage.removeItem('token');
      setToken(null);
    } finally {
      setLoading(false);
    }
  };

  // Fonctions admin
  const admin = {
    // Statistiques
    getStats: async () => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/stats`, {
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Get stats error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    // Gestion des utilisateurs
    getUsers: async () => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/users`, {
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Get users error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    createUser: async (userData) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/users`, {
          method: 'POST',
          headers: getAuthHeaders(),
          body: JSON.stringify(userData)
        });
        return await response.json();
      } catch (error) {
        console.error('Create user error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    updateUser: async (id, userData) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/users/${id}`, {
          method: 'PUT',
          headers: getAuthHeaders(),
          body: JSON.stringify(userData)
        });
        return await response.json();
      } catch (error) {
        console.error('Update user error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    deleteUser: async (id) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/users/${id}`, {
          method: 'DELETE',
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Delete user error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    // Gestion des demandes d'attestation
    getRequests: async () => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/requests`, {
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Get requests error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    getRequestById: async (id) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/requests/${id}`, {
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Get request by id error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    updateRequest: async (id, requestData) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/requests/${id}`, {
          method: 'PUT',
          headers: getAuthHeaders(),
          body: JSON.stringify(requestData)
        });
        return await response.json();
      } catch (error) {
        console.error('Update request error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    deleteRequest: async (id) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/requests/${id}`, {
          method: 'DELETE',
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Delete request error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    downloadRequestDocument: async (requestId, documentType) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/requests/${requestId}/download/${documentType}`, {
          method: 'GET',
          headers: getAuthHeaders()
        });
        
        if (response.ok) {
          const blob = await response.blob();
          const url = window.URL.createObjectURL(blob);
          return { success: true, data: { url } };
        } else {
          const data = await response.json();
          return { success: false, message: data.message || 'Erreur de téléchargement' };
        }
      } catch (error) {
        console.error('Download request document error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    // Générer une attestation
    generateAttestation: async (requestId, templateData) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/requests/${requestId}/generate-attestation`, {
          method: 'POST',
          headers: getAuthHeaders(),
          body: JSON.stringify(templateData)
        });
        return await response.json();
      } catch (error) {
        console.error('Generate attestation error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    // Gestion des modèles d'attestation
    getTemplates: async () => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/templates`, {
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Get templates error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    createTemplate: async (templateData) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/templates`, {
          method: 'POST',
          headers: getAuthHeaders(),
          body: JSON.stringify(templateData)
        });
        return await response.json();
      } catch (error) {
        console.error('Create template error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    updateTemplate: async (id, templateData) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/templates/${id}`, {
          method: 'PUT',
          headers: getAuthHeaders(),
          body: JSON.stringify(templateData)
        });
        return await response.json();
      } catch (error) {
        console.error('Update template error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    deleteTemplate: async (id) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/templates/${id}`, {
          method: 'DELETE',
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Delete template error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    // Gestion des paramètres
    getSettings: async () => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/settings`, {
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Get settings error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    updateSettings: async (settingsData) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/settings`, {
          method: 'PUT',
          headers: getAuthHeaders(),
          body: JSON.stringify(settingsData)
        });
        return await response.json();
      } catch (error) {
        console.error('Update settings error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    // Exporter des données
    exportData: async (type, format) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/admin/export/${type}?format=${format}`, {
          headers: getAuthHeaders()
        });
        
        if (response.ok) {
          const blob = await response.blob();
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `export-${type}-${new Date().toISOString().split('T')[0]}.${format}`;
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
          return { success: true };
        } else {
          const data = await response.json();
          return { success: false, message: data.message };
        }
      } catch (error) {
        console.error('Export data error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    }
  };

  // Fonctions pour les étudiants (demandes d'attestation)
  const student = {
    // Soumettre une demande d'attestation
    submitRequest: async (requestData) => {
      try {
        const formData = new FormData();
        
        // Ajouter les champs textuels
        Object.keys(requestData).forEach(key => {
          if (key !== 'files') {
            formData.append(key, requestData[key]);
          }
        });
        
        // Ajouter les fichiers
        if (requestData.files && Array.isArray(requestData.files)) {
          requestData.files.forEach((file, index) => {
            if (file instanceof File) {
              formData.append(`files[${index}]`, file);
            }
          });
        }
        
        const response = await fetch(`${apiConfig.baseURL}/student/requests`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`
          },
          body: formData
        });
        
        return await response.json();
      } catch (error) {
        console.error('Submit request error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    // Récupérer les demandes de l'étudiant
    getMyRequests: async () => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/student/my-requests`, {
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Get my requests error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    // Télécharger un document d'attestation
    downloadMyDocument: async (requestId) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/student/requests/${requestId}/download`, {
          headers: getAuthHeaders()
        });
        
        if (response.ok) {
          const blob = await response.blob();
          const url = window.URL.createObjectURL(blob);
          return { success: true, data: { url } };
        } else {
          const data = await response.json();
          return { success: false, message: data.message };
        }
      } catch (error) {
        console.error('Download my document error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    },

    // Annuler une demande
    cancelRequest: async (requestId) => {
      try {
        const response = await fetch(`${apiConfig.baseURL}/student/requests/${requestId}/cancel`, {
          method: 'POST',
          headers: getAuthHeaders()
        });
        return await response.json();
      } catch (error) {
        console.error('Cancel request error:', error);
        return { success: false, message: 'Erreur de connexion' };
      }
    }
  };

  // Vérifier l'authentification initiale
  useEffect(() => {
    fetchUser();
  }, [token]);

  // Vérifier si l'utilisateur est admin
  const isAdmin = user && user.type === 'admin';
  const isStudent = user && user.type === 'student';

  const value = {
    user,
    login,
    register,
    logout,
    loading,
    admin,
    student,
    token,
    isAdmin,
    isStudent
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};