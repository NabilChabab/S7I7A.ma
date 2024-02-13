<template>
    <div>
      <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input
          type="text"
          class="form-control"
          id="name"
          v-model="name"
        />
        <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
      </div>
  
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input
          type="email"
          class="form-control"
          id="email"
          aria-describedby="emailHelp"
          v-model="email"
        />
        <span v-if="errors.email" class="text-danger">{{ errors.email }}</span>
      </div>
  
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input
          type="password"
          class="form-control"
          id="password"
          v-model="password"
        />
        <span v-if="errors.password" class="text-danger">{{ errors.password }}</span>
      </div>
  
      <div class="mb-3">
        <label for="confirmPassword" class="form-label">Confirm Password</label>
        <input
          type="password"
          class="form-control"
          id="confirmPassword"
          v-model="confirmPassword"
        />
        <span v-if="errors.confirmPassword" class="text-danger">{{ errors.confirmPassword }}</span>
      </div>
  
      <div class="mb-3 form-check">
        <input
          type="checkbox"
          class="form-check-input"
          id="terms"
          v-model="acceptTerms"
        />
        <label class="form-check-label" for="terms">I agree to the terms and conditions</label>
        <span v-if="errors.acceptTerms" class="text-danger">{{ errors.acceptTerms }}</span>
      </div>
  
      <button type="submit" class="btn btn-primary" @click="registerUser">Submit</button>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        name: "",
        email: "",
        password: "",
        confirmPassword: "",
        acceptTerms: false,
        errors: {}
      };
    },
    methods: {
      registerUser() {
        this.errors = {};
  
        if (!this.name) {
          this.errors.name = "Full name is required.";
        }
        if (!this.email) {
          this.errors.email = "Email is required.";
        } else if (!this.validEmail(this.email)) {
          this.errors.email = "Invalid email format.";
        }
        if (!this.password) {
          this.errors.password = "Password is required.";
        }
        if (!this.confirmPassword) {
          this.errors.confirmPassword = "Please confirm your password.";
        } else if (this.password !== this.confirmPassword) {
          this.errors.confirmPassword = "Passwords do not match.";
        }
        if (!this.acceptTerms) {
          this.errors.acceptTerms = "Please accept the terms and conditions.";
        }
  
        if (Object.keys(this.errors).length === 0) {
          axios.post("http://127.0.0.1:8000/api/register", {
            name: this.name,
            email: this.email,
            password: this.password
          })
          .then((response) => {
            console.log(response.data);
            this.$router.push('/login')

            
          })
          .catch((error) => {
            console.error(error);
            alert("Error occurred while registering user.");
          });
        }
      },
      validEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
      }
    }
  };
  </script>
  
  <style>
  /* Add custom styles here */
  </style>
  