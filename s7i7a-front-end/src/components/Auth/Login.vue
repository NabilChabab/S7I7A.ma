<template>
  <div>
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
      <span v-if="errors.password" class="text-danger">{{
        errors.password
      }}</span>
    </div>

    <button type="submit" class="btn btn-primary" @click="loginUser">
      Submit
    </button>
  </div>
</template>

<script>
import axios from "axios"; 

export default {
  data() {
    return {
      email: "",
      password: "",
      errors: {},
    };
  },
  methods: {
    loginUser() {
      this.errors = {};

      if (!this.email) {
        this.errors.email = "Email is required.";
      } else if (!this.validEmail(this.email)) {
        this.errors.email = "Invalid email format.";
      }
      if (!this.password) {
        this.errors.password = "Password is required.";
      }

      if (Object.keys(this.errors).length === 0) {
        axios
          .post("http://127.0.0.1:8000/api/login", {
            email: this.email,
            password: this.password,
          })
          .then((response) => {
            console.log(response.data);
            const { message, redirect,token } = response.data;

            if (message === "Admin login successful") {
              localStorage.setItem("token", token);
              this.$router.push(redirect);
            }else if (message === "Doctor login successful") {
              localStorage.setItem("token", token);
              this.$router.push(redirect);
            }
            else if (message === "Patient login successful") {
              localStorage.setItem("token", token);
              this.$router.push(redirect);
            }
             else {
              alert("Unknown role.");
            }
          })
          .catch((error) => {
            console.error(error);
            alert("Error occurred while logging in.");
          });
      }
    },
    
    validEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },
  },
};

</script>

<style>
/* Add custom styles here */
</style>
