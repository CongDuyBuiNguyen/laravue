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
              <input type="file" class="form-control" @change="onFileChange">
              <button class="btn btn-success">Upload</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="filter-container">
      <el-input v-model="listQuery.name_file" :placeholder="'File name'" style="width: 225px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-select v-model="listQuery.sort" style="width: 140px" class="filter-item" @change="handleFilter">
        <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" />
      </el-select>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        {{ $t('table.search') }}
      </el-button>
    </div>

    <pagination
      v-show="total>0"
      :total="total"
      :page.sync="listQuery.page"
      :limit.sync="listQuery.limit"
      :page-sizes.sync="listQuery.pageSizes"
      @pagination="getData"
    />
    <el-table
      ref="dragTable"
      :data="list"
      row-key="id"
      border
      fit
      highlight-current-row
      style="width: 100%"
      @sort-change="sortChange"
    >
      <el-table-column align="center" prop="id" label="ID" :min-width="5" sortable="custom">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" prop="movie" label="Name File" :min-width="7" sortable>
        <template slot-scope="scope">
          <span>{{ scope.row.name_file }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" prop="website" label="User Upload" :min-width="7" sortable>
        <template slot-scope="scope">
          <span>{{ scope.row.user_uploaded }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Date" :min-width="7">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>
      <el-table-column class-name="status-col" label="Status" :min-width="7">
        <template slot-scope="scope">
          <el-tag :type="scope.row.status | statusFilter">
            {{ scope.row.status }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column label="Action" align="center" :min-width="15" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button v-if="scope.row.status === 'uploaded'" size="small" style="color: #1e6abc" @click="callWorkerSpleeter(scope.row)">
            Spleeter
          </el-button>
          <el-button v-if="scope.row.status === 'spleeted'" size="small" style="color: #1ae1e8" @click="downloadSpleetedFile(scope.row.path_vocals)">
            Download Vocals
          </el-button>
          <el-button v-if="scope.row.status === 'spleeted'" size="small" style="color: #f10905" @click="downloadSpleetedFile(scope.row.path_accompaniment)">
            Download Accompaniment
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <div class="vld-parent">
      <loading
        :active.sync="isLoading"
        :can-cancel="false"
        :color="'#f5de07'"
      />
    </div>
  </div>
</template>

<script>
import { uploadFile, showListFile, downloadFile, sendWorkerMessage } from '@/api/spleeter';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import Pagination from '@/components/Pagination';
import waves from '@/directive/waves';

export default {
  components: {
    Loading,
    Pagination,
  },
  directives: {
    waves,
  },
  filters: {
    statusFilter(status) {
      const statusMap = {
        uploaded: 'success',
        inProgress: 'info',
        spleeted: 'danger',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      name: '',
      file: '',
      success: '',
      isLoading: false,
      list: [],
      rowDetail: [],
      total: 0,
      listQuery: {
        page: 1,
        limit: 25,
        pageSizes: [10, 25, 50, 100],
        name_file: undefined,
        sort: '-id',
      },
      sortOptions: [
        { label: 'Oldest', key: '+id' },
        { label: 'Newest', key: '-id' },
      ],
      polling: null,
    };
  },
  beforeDestroy() {
    clearInterval(this.polling);
  },
  created() {
    this.getData();
    this.fetchDataUsual();
  },
  methods: {
    onFileChange(e){
      this.isLoading = true;
      this.file = e.target.files[0];
      this.isLoading = false;
    },
    formSubmit(e) {
      e.preventDefault();
      const currentObj = this;
      const formData = new FormData();
      formData.append('file', this.file);
      formData.append('userUploaded', this.$store.getters.name);
      uploadFile(formData).then(response => {
        this.isLoading = true;
        currentObj.success = response.data;
        this.isLoading = false;
        this.getData();
      });
    },
    async getData() {
      this.isLoading = true;
      const response = await showListFile();
      let data;
      if (response['status'] === 200) {
        data = response['data']['list_file'];
      }
      data = data.filter(item => {
        let fileName;
        if (this.listQuery.name_file) {
          fileName = this.listQuery.name_file.toLowerCase();
        }
        return !(fileName && item.name_file.toLowerCase().indexOf(fileName) < 0);
      });
      if (this.listQuery.sort === '-id') {
        data = data.reverse();
      }
      this.list = data.filter((item, index) => index < this.listQuery.limit * this.listQuery.page && index >= this.listQuery.limit * (this.listQuery.page - 1));
      this.total = data.length;
      this.isLoading = false;
      return data;
    },
    fetchDataUsual() {
      const self = this;
      this.polling = setInterval(function(){
        self.getData();
      }, 10000);
    },
    handleFilter() {
      this.listQuery.page = 1;
      this.getData();
    },
    sortChange(data) {
      const { prop, order } = data;
      if (prop === 'id') {
        this.sortByID(order);
      }
    },
    sortByID(order) {
      if (order === 'ascending') {
        this.listQuery.sort = '+id';
      } else {
        this.listQuery.sort = '-id';
      }
      this.handleFilter();
    },
    callWorkerSpleeter(data) {
      const self = this;
      sendWorkerMessage({ 'message': data });
      setTimeout(function(){
        self.getData();
      }, 2000);
    },
    downloadSpleetedFile(pathFile) {
      downloadFile({
        'pathFile': pathFile,
      }).then(res => {
        const blob = new Blob([res], { type: 'application/*' });
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        const pathFileSplit = pathFile.split('output/')[1].split('/');
        link.download = `${pathFileSplit[0]}_${pathFileSplit[1]}`;
        link._target = 'blank';
        link.click();
      });
    },
  },
};
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
.container {
  padding: 32px;
  background-color: rgb(240, 242, 245);
  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}
</style>
