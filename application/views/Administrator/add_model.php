<div class="row" id="model">
	<div class="col-md-4 col-md-offset-4" style="padding-bottom: 20px;">
		<form action="" v-on:submit.prevent="saveModel">
			<div class="form-group">
				<label for="email">Model Name:</label>
				<input type="text" class="form-control" v-model="model_name" style="height: 35px;border-radius: 0px !important;">
			</div>
			<div class="form-group" style="text-align: center;margin-top:15px;">

				<button type="submit" class="btn btn-primary" style="padding: 3px 20px;">Submit</button>
			</div>
		</form>
	</div>

	<div class="col-md-8 col-md-offset-2" style="padding-top: 20px;border-top:1px solid #ccc">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>SL</th>
					<th>Model Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(model,index) in models" :key="index">
					<td>{{index+1}}</td>
					<td>{{model.model_name}}</td>
					<td>
						<button v-on:click.prevent="edit(model)"><i class="fa fa-pencil"></i></button>
						<button v-on:click.prevent="deleteModel(model.model_id)"><i class="fa fa-trash"></i></button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script> -->

<script>
	// Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#model',
		data() {
			return {
				model_id: '',
				model_name: '',
				models: [],
			}
		},
		created() {
			this.getModels();
		},
		methods: {
			getModels() {
				axios.get('/get_models').then(res => {
					this.models = res.data;
				})
			},
			saveModel() {
				if (this.model_name == '') {
					alert('Model Name Field is Empty!');
					return;
				}
				let filter = {
					model_id: this.model_id,
					model_name: this.model_name,
				}
				let url = '';
				if (this.model_id == '') {
					url = '/addModel';
				} else {
					url = '/updateModel';
				}

				axios.post(url, filter).then(res => {
					if (res.data.status) {
						alert(res.data.message);
						this.model_name = '';
						this.getModels();
					} else {
						alert(res.data.message);
					}

				})
			},
			edit(data) {
				this.model_id = data.model_id;
				this.model_name = data.model_name;
			},

			deleteModel(id) {
				axios.get('/deleteModel/' + id).then(res => {
					if (res.data.status) {
						alert(res.data.message);
						this.getModels();
					} else {
						alert(res.data.message);
					}
				})
			}



		}
	})
</script>