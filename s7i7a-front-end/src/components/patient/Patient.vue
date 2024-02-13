<template>
    <div>
        <h1>welcome patient</h1>
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
