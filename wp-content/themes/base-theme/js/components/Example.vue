<template>
  <div class="row">
    <div class="col-12 text-center d-flex justify-content-center align-items-center flex-column">
      <i class="fas fa-check-circle"></i>
      <h3>{{ text }}</h3>
    </div>
  </div>
</template>
<script>
export default {
  mounted() {
    console.log("Mounted soy el vue!");
    this.$swal({
      title: 'Bien hecho!',
      text: 'base-theme se ha instalado correctamente',
      icon: 'success'
    });
  },
  data: function() {
    return {
      text: "Base theme instalado!",
    };
  },
};
</script>
