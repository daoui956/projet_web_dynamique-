import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import './Register.css';

const Register = ({ onToggleForm }) => {
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    password: '',
    type: '', 
    agreeTerms: false
  });

  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState('');
  const { register } = useAuth();

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData({
      ...formData,
      [name]: type === 'checkbox' ? checked : value
    });

    if (errors[name]) {
      setErrors({
        ...errors,
        [name]: ''
      });
    }
    if (errors.general) {
      setErrors({});
    }
  };

  const handleRoleSelect = (role) => {
    setFormData({
      ...formData,
      type: role
    });

    if (errors.type) {
      setErrors({
        ...errors,
        type: ''
      });
    }
  };

  const validateForm = () => {
    const newErrors = {};

    if (!formData.firstName.trim()) newErrors.firstName = 'Le prénom est requis';
    if (!formData.lastName.trim()) newErrors.lastName = 'Le nom est requis';

    if (!formData.email) {
      newErrors.email = 'L\'email est requis';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'L\'email est invalide';
    }

    if (!formData.password) {
      newErrors.password = 'Le mot de passe est requis';
    } else if (formData.password.length < 6) {
      newErrors.password = 'Le mot de passe doit contenir au moins 6 caractères';
    }

    if (!formData.type) {
      newErrors.type = 'Veuillez sélectionner votre profil';
    }

    if (!formData.agreeTerms) {
      newErrors.agreeTerms = 'Vous devez accepter les conditions d\'utilisation';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSuccess('');
    setErrors({});

    if (!validateForm()) return;

    const userData = {
      name: `${formData.firstName} ${formData.lastName}`,
      email: formData.email,
      password: formData.password,
      type: formData.type
    };

    setLoading(true);
    const result = await register(userData);

    if (result.success) {
      setSuccess(result.message);
      setFormData({
        firstName: '',
        lastName: '',
        email: '',
        password: '',
        type: '',
        agreeTerms: false
      });
    } else {
      setErrors(result.errors || { general: result.message });
    }

    setLoading(false);
  };

  const getRoleIcon = (role) => {
    switch (role) {
      case 'student': return '';
      case 'teacher': return '';
      case 'enterprise': return '';
      default: return '';
    }
  };

  return (
    <div className="register-container">
      <div className="register-header">
        <h1>Créer votre compte</h1>
        <p>Rejoignez notre plateforme et commencez votre parcours</p>
      </div>

      <div className="register-form">
        {success && (
          <div className="success-message">
            <div className="success-icon">✓</div>
            <div className="success-content">
              <h4>Compte créé avec succès !</h4>
              <p>{success}</p>
            </div>
          </div>
        )}

        {errors.general && (
          <div className="error-message general-error">
            <div className="error-icon"></div>
            <div className="error-content">
              <h4>Erreur</h4>
              <p>{errors.general}</p>
            </div>
          </div>
        )}

        <form onSubmit={handleSubmit}>
          {/* Role Selection */}
          <div className="form-section">
            <label className="section-label">Je suis :</label>
            <div className="role-selection-grid">
              <div 
                className={`role-card ${formData.type === 'student' ? 'selected' : ''} ${errors.type ? 'error' : ''}`}
                onClick={() => handleRoleSelect('student')}
              >
                <div className="role-icon">{getRoleIcon('student')}</div>
                <div className="role-content">
                  <h3>Étudiant</h3>
                  <p>Accédez aux cours, ressources et suivez votre progression</p>
                </div>
                <div className="selection-indicator"></div>
              </div>

              <div 
                className={`role-card ${formData.type === 'teacher' ? 'selected' : ''} ${errors.type ? 'error' : ''}`}
                onClick={() => handleRoleSelect('teacher')}
              >
                <div className="role-icon">{getRoleIcon('teacher')}</div>
                <div className="role-content">
                  <h3>Enseignant</h3>
                  <p>Créez et gérez des classes, guidez vos étudiants</p>
                </div>
                <div className="selection-indicator"></div>
              </div>

              <div 
                className={`role-card ${formData.type === 'enterprise' ? 'selected' : ''} ${errors.type ? 'error' : ''}`}
                onClick={() => handleRoleSelect('enterprise')}
              >
                <div className="role-icon">{getRoleIcon('enterprise')}</div>
                <div className="role-content">
                  <h3>Entreprise</h3>
                  <p>Gérez votre profil et publiez des offres de stage</p>
                </div>
                <div className="selection-indicator"></div>
              </div>
            </div>
            {errors.type && <span className="field-error">{errors.type}</span>}
          </div>

          {/* Personal Information */}
          <div className="form-section">
            <label className="section-label">Informations personnelles</label>
            <div className="form-grid">
              <div className="form-group">
                <div className="input-container">
                  <input
                    type="text"
                    name="firstName"
                    value={formData.firstName}
                    onChange={handleChange}
                    className={errors.firstName ? 'error' : ''}
                    placeholder=" "
                  />
                  <label className="floating-label">Prénom</label>
                  <span className="input-icon"></span>
                </div>
                {errors.firstName && <span className="field-error">{errors.firstName}</span>}
              </div>

              <div className="form-group">
                <div className="input-container">
                  <input
                    type="text"
                    name="lastName"
                    value={formData.lastName}
                    onChange={handleChange}
                    className={errors.lastName ? 'error' : ''}
                    placeholder=" "
                  />
                  <label className="floating-label">Nom</label>
                  <span className="input-icon"></span>
                </div>
                {errors.lastName && <span className="field-error">{errors.lastName}</span>}
              </div>

              <div className="form-group full-width">
                <div className="input-container">
                  <input
                    type="email"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                    className={errors.email ? 'error' : ''}
                    placeholder=" "
                  />
                  <label className="floating-label">Adresse email</label>
                  <span className="input-icon"></span>
                </div>
                {errors.email && <span className="field-error">{errors.email}</span>}
              </div>

              <div className="form-group full-width">
                <div className="input-container">
                  <input
                    type="password"
                    name="password"
                    value={formData.password}
                    onChange={handleChange}
                    className={errors.password ? 'error' : ''}
                    placeholder=" "
                  />
                  <label className="floating-label">Mot de passe</label>
                  <span className="input-icon"></span>
                </div>
                {errors.password && <span className="field-error">{errors.password}</span>}
                <div className="password-hint">Minimum 6 caractères</div>
              </div>
            </div>
          </div>

          {/* Terms and Conditions */}
          <div className="form-section">
            <div className={`terms-group ${errors.agreeTerms ? 'error' : ''}`}>
              <label className="checkbox-container">
                <input
                  type="checkbox"
                  name="agreeTerms"
                  id="agreeTerms"
                  checked={formData.agreeTerms}
                  onChange={handleChange}
                />
                <span className="checkmark"></span>
                <span className="terms-text">
                  J'accepte les <a href="#" className="terms-link">Conditions d'utilisation</a> et la <a href="#" className="terms-link">Politique de confidentialité</a>
                </span>
              </label>
            </div>
            {errors.agreeTerms && <span className="field-error">{errors.agreeTerms}</span>}
          </div>

          {/* Submit Button */}
          <button 
            type="submit" 
            disabled={loading || !formData.agreeTerms || !formData.type} 
            className={`submit-btn ${loading ? 'loading' : ''}`}
          >
            {loading ? (
              <>
                <div className="spinner"></div>
                Création du compte...
              </>
            ) : (
              'Créer mon compte'
            )}
          </button>

          {/* Login Link */}
          <div className="auth-switch">
            <p>
              Déjà un compte ?{' '}
              <button type="button" className="switch-link" onClick={onToggleForm}>
                Se connecter
              </button>
            </p>
          </div>
        </form>
      </div>
    </div>
  );
};

export default Register;