// src/services/TeacherService.js
import axios from 'axios';

const API_URL = 'http://localhost:8000/api'; // Remplacez par l'URL de votre API Laravel

const getAuthHeader = () => {
  const token = localStorage.getItem('token');
  return {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
  };
};

class TeacherService {
  getDashboardStats() {
    return axios.get(`${API_URL}/teacher/dashboard`, getAuthHeader());
  }

  getCourses() {
    return axios.get(`${API_URL}/teacher/courses`, getAuthHeader());
  }

  getCourseStudents(courseId) {
    return axios.get(`${API_URL}/teacher/courses/${courseId}/students`, getAuthHeader());
  }

  getAssignments(status = null) {
    let url = `${API_URL}/teacher/assignments`;
    if (status) {
      url += `?status=${status}`;
    }
    return axios.get(url, getAuthHeader());
  }

  submitGrade(gradeData) {
    return axios.post(`${API_URL}/teacher/grades`, gradeData, getAuthHeader());
  }

  createCourse(courseData) {
    return axios.post(`${API_URL}/teacher/courses`, courseData, getAuthHeader());
  }

  createAssignment(assignmentData) {
    return axios.post(`${API_URL}/teacher/assignments`, assignmentData, getAuthHeader());
  }
}

export default new TeacherService();