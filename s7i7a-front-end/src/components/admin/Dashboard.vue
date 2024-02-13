<template>
    <div>
        <h1>Welcome Admin</h1>
        <a href="#" @click="logoutUser">Logout</a>
    </div>
</template>

<script>
export default {
  methods: {
    logoutUser() {
      localStorage.removeItem("token");
      this.$router.push("/login");
    },
  }
};
</script>
