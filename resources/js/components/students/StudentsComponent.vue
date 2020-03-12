<template>
    <div>
        <h1 class="text-center">Alunos</h1><hr>
        <b-button v-b-toggle.collapse-1 variant="primary" style="margin-bottom: 15px">Filtros</b-button>
                <b-collapse id="collapse-1" class="mt-2">
                    <b-card>
                        <b-row>
                            <b-col md="4">
                                <b-form-group
                                    class="mb-0"
                                    label="Nome:"
                                    label-for="name"
                                >
                                    <b-form-input
                                        id="name"
                                        type="text"
                                        v-model="filter.name"
                                    ></b-form-input>
                                </b-form-group>
                            </b-col>
                            <b-col md="4">
                                <b-form-group
                                    class="mb-0"
                                    label="E-mail:"
                                    label-for="email"
                                >
                                    <b-form-input
                                        id="email"
                                        type="text"
                                        v-model="filter.email"
                                    ></b-form-input>
                                </b-form-group>
                            </b-col>
                        </b-row>
                    </b-card>
                </b-collapse>
        <b-row>
            
            <b-col md="12">
                <b-table
                    :fields="fields"
                    :items="students"
                    :current-page="pagination.currentPage"
                    :per-page="pagination.perPage"
                    striped bordered small responsive :busy="busy" show-empty
                    head-variant="light"
                    empty-filtered-text="Nenhum registro encontrado"
                    filter=null
                    :filter-function="processFilters"
                    @filtered="onFiltered"
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
                <b-button variant="primary" v-b-modal.add-student>Adicionar</b-button>
            </b-col>
        </b-row>

        <!-- modal de inclusão/alteração -->
        <b-modal
            id="add-student"
            title="Adicionar aluno"
        >
            <b-form-group
                label="Nome:"
                label-for="name"
            >
                <b-form-input
                    id="name"
                    v-model="student.name"
                    required
                ></b-form-input>
            </b-form-group>
            <b-form-group
                label="E-mail:"
                label-for="email"
            >
                <b-form-input
                    id="email"
                    type="email"
                    v-model="student.email"
                    required
                ></b-form-input>
            </b-form-group>
            <b-form-group
                label="Data de Nascimento:"
                label-for="birth-date"
            >
                <b-form-input
                    id="birth-date"
                    type="date"
                    v-model="student.birth_date"
                    required
                ></b-form-input>
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
        <b-modal id="delete-student" title="Excluir aluno" button-size="sm"
            :header-bg-variant="'danger'" 
            :header-text-variant="'light'"
            :footer-bg-variant="'danger'"
            >
            <p>Deseja realmente excluir esse aluno?</p>
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
            students: [],
            student:{
                id: null,
                name: "",
                email: "",
                birth_date: ""
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
                { key: "name", label: "Nome", sortable: true, class: "text-center" },
                { key: "email", label: "E-mail", sortable: true, class: "text-center" },
                { key: "birth_date", label: "Data de Nascimento", sortable: true, class: "text-center",
                    formatter: value => {
                        if (value != null){
                            let date = value.split("-");
                            return date[2]+"/"+date[1]+"/"+date[0]
                        }else{
                            return "";
                        }
                        
                    }
                },
            ],
            busy: false,
            filter:{
                name: "",
                email: ""
            }
        }
    },
    created(){
        this.busy = true;
        this.list();
    },
    methods:{
        list(){
            axios("students/allStudents").then(response => {
                let resp = response.data;
                if (resp.success){
                    this.students = resp.students;
                    this.pagination.totalRecords = this.students.length;
                }
                this.busy = false;
            }).catch(error => {
                console.error("Erro ao listar os alunos. "+error);
            });;
        },
        save(){
            if (this.student.id != null){
                this.update();
                return false;
            }

            axios.post("students", {
                data: this.student
            }).then(response => {
                let resp = response.data;
                if (resp.success){
                    this.list();
                    this.$bvModal.hide("add-student");
                    this.cleanFields();
                }
            }).catch(error => {
                console.error("Erro ao cadastrar o aluno. "+error);
            });
        },
        edit(id){
            this.selectedID = id;
            axios.get(`students/${id}/edit`,
            { params: { id } }).then(response => {
                let resp = response.data;
                if (resp.success){
                   this.student = resp.student;
                   this.$bvModal.show("add-student");
                }
            }).catch(error => {
                console.error("Erro ao buscar dados do aluno. "+error);
            });
        },
        update(){
            axios.put(`students/${this.student.id}`,
            { id: this.student.id, data: this.student } ).then(response => {
                let resp = response.data;
                if (resp.success){
                    this.cleanFields();
                    this.$bvModal.hide("add-student");
                    this.list();
                }
            }).catch(error => {
                console.error("Erro ao editar o aluno. "+error);
            });
        },
        remove(confirmation, id = null){
            if (!confirmation){
                this.selectedID = id;
                this.$bvModal.show("delete-student");
                return false;
            }

            axios.delete(`students/${this.id}`, 
            { params: { id: this.selectedID} }).then(response => {
                let resp = response.data;
                if (resp.success) this.list();
                
                this.$bvModal.hide("delete-student");
            }).catch(error => {
                console.error("Erro ao remover o aluno. "+error);
            });
        },
        cleanFields(){
            this.student.id = null;
            this.student.name = "";
            this.student.email = "";
            this.student.birth_date = "";
        },
        processFilters(row){
            console.log(row.name == this.filter.name);
            if (this.filter.name != "" && this.filter.name != null){
                if (row.name.toLowerCase().indexOf(this.filter.name.toLowerCase()) == -1) return false;
            }

            if (this.filter.email != null && this.filter.email != ""){
                if (row.email.toLowerCase().indexOf(this.filter.email.toLowerCase()) == -1) return false;
            }

            return true;
        },
        onFiltered(filteredItems){
            this.pagination.totalRecords = filteredItems.length;
            this.pagination.currentPage = 1;
        }
    }

}
</script>