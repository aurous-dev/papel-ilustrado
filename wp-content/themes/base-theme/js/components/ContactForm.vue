<template>
  <div class="col-12">
    <form @submit.prevent="handleSubmit" action="contact" method="POST">
      <div class="form-group">
        <label for="nombre">Nombre</label>
        <input
          type="text"
          v-model="user.name"
          v-validate="'required|min:2'"
          name="nombre"
          placeholder="Nombre"
          class="form-control"
          :class="{ 'is-invalid': errors.has('nombre') }"
        />
        <div v-if="errors.has('nombre')" class="invalid-feedback">{{ errors.first("nombre") }}</div>
      </div>
      <div class="form-group">
        <label for="email">Mail</label>
        <input
          type="email"
          v-model="user.email"
          v-validate="'required|email'"
          placeholder="Email"
          id="email"
          name="email"
          class="form-control"
          :class="{ 'is-invalid': errors.has('email') }"
        />
        <div v-if="errors.has('email')" class="invalid-feedback">{{ errors.first("email") }}</div>
      </div>
      <div class="form-group">
        <label for="subject">Asunto</label>
        <input
          type="text"
          v-model="user.subject"
          v-validate="'required|min:10'"
          name="asunto"
          placeholder="Asunto"
          class="form-control"
          :class="{ 'is-invalid': errors.has('asunto') }"
        />
        <div v-if="errors.has('asunto')" class="invalid-feedback">{{ errors.first("asunto") }}</div>
      </div>
      <div class="form-group">
        <label for="message">Mensaje</label>
        <textarea
          name="mensaje"
          placeholder="Mensaje"
          v-validate="'required|min:20'"
          v-model="user.message"
          class="form-control"
          :class="{ 'is-invalid': errors.has('mensaje') }"
        ></textarea>
        <div v-if="errors.has('mensaje')" class="invalid-feedback">{{ errors.first("mensaje") }}</div>
      </div>
      <button class="btn btn-primary mt-3" type="submit">Enviar</button>
      <!-- <button-spinner
        class="btn btn-primary mt-3"
        :is-loading="isLoading"
        :disabled="isLoading"
        :status="status"
        type="submit"
      >
        <span>Enviar</span>
      </button-spinner>-->
    </form>
  </div>
</template>
<script>
import axios from "axios";
import { VueReCaptcha } from "vue-recaptcha-v3";
import ButtonSpinner from "vue-button-spinner";

export default {
  name: "formu",
  mounted() {
    console.log("Formulario ejecutado");
  },
  components: {
    ButtonSpinner,
  },
  data() {
    return {
      user: {
        name: "",
        email: "",
        subject: "",
        message: "",
      },
      submitted: false,
      isLoading: false,
      status: "",
      homeurl: "",
      formid: 5 /* Your contact 7 form ID */,
    };
  },
  mounted() {
    const localUrl = window.location.href;
    this.homeurl = localUrl;

    if (this.homeurl[this.homeurl.length - 1] === "/") {
      const newUrl = this.homeurl.split("");
      newUrl.pop();

      this.homeurl = newUrl.join("");
    }

    console.log(this.homeurl);
  },
  methods: {
    handleSubmit() {
      this.isLoading = true;
      this.submitted = true;
      var bodyFormData = new FormData();
      bodyFormData.set("your-name", this.user.name);
      bodyFormData.set("your-email", this.user.email);
      bodyFormData.set("your-subject", this.user.subject);
      bodyFormData.set("your-message", this.user.message);
      this.$validator.validate().then((valid) => {
        if (valid) {
          axios({
            method: "post",
            url: `${this.homeurl}/wp-json/contact-form-7/v1/contact-forms/${this.formid}/feedback` /* This has to be replaced by you form api URL */,
            data: bodyFormData,
            config: { headers: { "Content-Type": "multipart/form-data" } },
          }).then((response) => {
            //   IF FOR IS SENT
            if (response.data.status == "mail_sent") {
              this.$swal({
                icon: "success",
                text: response.data.message,
              });
              Object.keys(this.user).forEach((key) => {
                this.user[key] = "";
              });
              this.errors.clear();
              this.$validator.reset();
              this.isLoading = false;
              //   IF HAS THANK YOU PAGE

              // setTimeout(() => {
              //   window.location.href = "/thankyou";
              // }, 300);

              //   IF HAS THANK YOU PAGE
            } else {
              //   IF FOR IS NOT SENT
              this.isLoading = false;
              this.$swal({
                icon: "error",
                text: response.data.message,
              });
            }
          });
        }
      });
    },
  },
};
</script>