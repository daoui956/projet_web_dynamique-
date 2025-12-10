import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import './Login.css';

const Login = ({ onToggleForm }) => {
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    rememberMe: false
  });
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const { login } = useAuth();

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

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrors({});
    setLoading(true);

    const result = await login(formData.email, formData.password);
    
    if (!result.success) {
      setErrors({ 
        general: result.message,
        ...result.errors 
      });
    }
    
    setLoading(false);
  };

  return (
    <div className="login-container">
      <div className="login-header">
        <h1>Content de vous revoir </h1>
        <p>Connectez-vous à votre compte pour continuer</p>
      </div>

      <div className="login-form">
        {errors.general && (
          <div className="error-message general-error">
            <div className="error-icon"></div>
            <div className="error-content">
              <h4>Erreur de connexion</h4>
              <p>{errors.general}</p>
            </div>
          </div>
        )}

        <form onSubmit={handleSubmit}>
          {/* Email Field */}
          <div className="form-section">
            <div className="form-group">
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

            {/* Password Field */}
            <div className="form-group">
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
            </div>
          </div>

          {/* Options */}
          <div className="login-options">
            <div className="remember-me">
              <label className="checkbox-container">
                <input
                  type="checkbox"
                  name="rememberMe"
                  id="rememberMe"
                  checked={formData.rememberMe}
                  onChange={handleChange}
                />
                <span className="checkmark"></span>
                <span className="checkbox-label">Se souvenir de moi</span>
              </label>
            </div>
            {/*<a href="#" className="forgot-password">
              Mot de passe oublié ?
            </a>*/}
          </div>

          {/* Submit Button */}
          <button 
            type="submit" 
            disabled={loading} 
            className={`submit-btn ${loading ? 'loading' : ''}`}
          >
            {loading ? (
              <>
                <div className="spinner"></div>
                Connexion...
              </>
            ) : (
              'Se connecter'
            )}
          </button>

          {/* Signup Link */}
          <div className="auth-switch">
            <p>
              Pas encore de compte ?{' '}
              <button type="button" className="switch-link" onClick={onToggleForm}>
                Créer un compte
              </button>
            </p>
          </div>
        </form>
      </div>
    </div>
  );
};

export default Login;