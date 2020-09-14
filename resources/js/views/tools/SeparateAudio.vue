<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">

          <div class="card-body">
            <div v-if="success !== ''" class="alert alert-success" role="alert">
              {{ success }}
            </div>
            <form enctype="multipart/form-data" @submit="formSubmit">
              <strong>Name:</strong>
              <input v-model="name" type="text" class="form-control">
              <strong>File:</strong>
              <input type="file" class="form-control" @change="onFileChange">
              <button class="btn btn-success">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      name: '',
      file: '',
      success: '',
    };
  },
  mounted() {
    console.log('Component mounted.');
  },
  methods: {
    onFileChange(e){
      console.log(e.target.files[0]);
      this.file = e.target.files[0];
    },
    formSubmit(e) {
      e.preventDefault();
      const currentObj = this;

      const config = {
        headers: { 'content-type': 'multipart/form-data' },
      };

      const formData = new FormData();
      formData.append('file', this.file);

      axios.post('/api/upload', formData, config)
        .then(function(response) {
          currentObj.success = response.data.success;
        })
        .catch(function(error) {
          currentObj.output = error;
        });
    },
  },
};
</script>
