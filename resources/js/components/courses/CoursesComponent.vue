<template>
    <div>
        <h1 class="text-center">Cursos</h1><hr>
        <b-row>
            <b-col md="12">
                <b-table
                    :fields="fields"
                    :items="courses"
                    :current-page="pagination.currentPage"
                    :per-page="pagination.perPage"
                    striped bordered small responsive :busy="busy" show-empty
                    head-variant="light"
                    empty-filtered-text="Nenhum registro encontrado"
                >
                    <template v-slot:table-busy>
                        <div class="text-center text-info my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Carregando...</strong>
                        </div>
                    </template>
                    <template v-slot:cell(edtExc)="row">
                        <b-button-group size="sm">
                            <b-button variant="primary" title="Editar" @click="edit(row.item.id)"><i class="fas fa-edit"></i></b-button>
                            <b-button variant="danger" title="Excluir" @click="remove(false, row.item.id)"><i class="fas fa-trash-alt"></i></b-button>
                        </b-button-group>
                    </template>
                </b-table>
            </b-col>

            <!-- Paginação -->
            <b-col md="4">
                <b-pagination
                    v-model="pagination.currentPage"
                    :total-rows="pagination.totalRecords"
                    :per-page="pagination.perPage"
                    align="fill"
                    class="my-0"
                    md="6"
                ></b-pagination>
            </b-col>
            <b-col md="4">
                <b-button variant="primary" v-b-modal.add-course>Adicionar</b-button>
            </b-col>
        </b-row>

        <!-- modal de inclusão/alteração -->
        <b-modal
            id="add-course"
            title="Adicionar curso"
        >
            <b-form-group
                label="Título:"
                label-for="title"
            >
                <b-form-input
                    id="title"
                    v-model="course.title"
                    required
                ></b-form-input>
            </b-form-group>
            <b-form-group
                label="Descrição:"
                label-for="description"
            >
                <b-form-textarea
                    id="description"
                    type="description"
                    v-model="course.description"
                    required
                ></b-form-textarea>
            </b-form-group>

            <template v-slot:modal-footer="{ ok, cancel }">
                <b-button variant="success" @click="save()">
                    Cadastrar
                </b-button>
                <b-button variant="danger" @click="cancel()">
                    Cancelar
                </b-button>
            </template>

        </b-modal>

        <!-- Modal para confirmação de exclusão -->
        <b-modal id="delete-course" title="Excluir curso" button-size="sm"
            :header-bg-variant="'danger'" 
            :header-text-variant="'light'"
            :footer-bg-variant="'danger'"
            >
            <p>Deseja realmente excluir esse curso?</p>
            <template v-slot:modal-footer="{ ok, cancel }">
                <b-button variant="success" @click="remove(true)">
                    OK
                </b-button>
                <b-button variant="light" @click="cancel()">
                    Cancelar
                </b-button>
            </template>
        </b-modal>

    </div>
</template>

<script>
export default {
    data(){
        return {
            courses: [],
            course:{
                id: null,
                title: "",
                description: "",
            },
            selectedID: 0,
            pagination:{
                currentPage: 1,
                perPage: 10,
                totalRecords: 1,
            },
            fields: [
                { key: "edtExc", label: " " },
                { key: "id", label: "Código", sortable: true, class: "text-center" },
                { key: "title", label: "Título", sortable: true, class: "text-center" },
                { key: "description", label: "Descrição", sortable: true, class: "text-center" },
            ],
            busy: false,
        }
    },
    created(){
        this.busy = true;
        this.list();
    },
    methods:{
        list(){
            axios("courses/allCourses").then(response => {
                let resp = response.data;
                if (resp.success){
                    this.courses = resp.courses;
                    this.pagination.totalRecords = this.courses.length;
                }
                this.busy = false;
            }).catch(error => {
                console.error("Erro ao listar os cursos. "+error);
            });;
        },
        save(){
            if (this.course.id != null){
                this.update();
                return false;
            }

            axios.post("courses", {
                data: this.course
            }).then(response => {
                let resp = response.data;
                if (resp.success){
                    this.list();
                    this.$bvModal.hide("add-course");
                    this.cleanFields();
                }
            }).catch(error => {
                console.error("Erro ao cadastrar o curso. "+error);
            });
        },
        edit(id){
            this.selectedID = id;
            axios.get(`courses/${id}/edit`,
            { params: { id } }).then(response => {
                let resp = response.data;
                if (resp.success){
                   this.course = resp.course;
                   this.$bvModal.show("add-course");
                }
            }).catch(error => {
                console.error("Erro ao buscar dados do cursos. "+error);
            });
        },
        update(){
            axios.put(`courses/${this.course.id}`,
            { id: this.course.id, data: this.course } ).then(response => {
                let resp = response.data;
                if (resp.success){
                    this.cleanFields();
                    this.$bvModal.hide("add-course");
                    this.list();
                }
            }).catch(error => {
                console.error("Erro ao editar o curso. "+error);
            });
        },
        remove(confirmation, id = null){
            if (!confirmation){
                this.selectedID = id;
                this.$bvModal.show("delete-course");
                return false;
            }

            axios.delete(`courses/${this.id}`, 
            { params: { id: this.selectedID} }).then(response => {
                let resp = response.data;
                if (resp.success) this.list();
                
                this.$bvModal.hide("delete-course");
            }).catch(error => {
                console.error("Erro ao remover o curso. "+error);
            });
        },
        cleanFields(){
            this.course.id = null;
            this.course.title = "";
            this.course.description = "";
        }
    }

}
</script>