<template>
    <div>
        <h1 class="text-center">Matrículas</h1><hr>
        <b-row>
            <b-col md="12">
                <b-table
                    :fields="fields"
                    :items="registrations"
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
                <b-button variant="primary" v-b-modal.add-registration>Adicionar</b-button>
            </b-col>
        </b-row>

        <!-- modal de inclusão/alteração -->
        <b-modal
            id="add-registration"
            title="Adicionar matrícula"
        >
            <b-form-group label="Aluno:">
                <b-form-input list="student" v-model="data.student" :disabled="students.length == 0"></b-form-input>
                <datalist id="student">
                    <option v-for="student in students" :key="student.id">{{ student.name }}</option>
                </datalist>
            </b-form-group>

            <b-form-group label="Curso:">
                <b-form-input list="course" v-model="data.course" :disabled="courses.length == 0"></b-form-input>
                <datalist id="course">
                    <option v-for="course in courses" :key="course.id">{{ course.title }}</option>
                </datalist>
            </b-form-group>
            <b-button variant="primary" @click="addCourse" :disabled="courses.length == 0 || students.length == 0"><i class="fas fa-plus"></i></b-button>

            <b-list-group>
                <b-list-group-item v-for="(course, index) in this.data.courses" :key="course">{{ course }} <b-button variant="danger" @click="removeCourse(index)" size="sm"><i class="fas fa-trash-alt"></i></b-button></b-list-group-item>
            </b-list-group>

            <template v-slot:modal-footer="{ ok, cancel }">
                <b-button variant="success" @click="save()" :disabled="courses.length == 0 || students.length == 0">
                    Cadastrar
                </b-button>
                <b-button variant="danger" @click="cancel()" :disabled="courses.length == 0 || students.length == 0">
                    Cancelar
                </b-button>
            </template>

        </b-modal>

        <!-- Modal para confirmação de exclusão -->
        <b-modal id="delete-registration" title="Excluir aluno" button-size="sm"
            :header-bg-variant="'danger'" 
            :header-text-variant="'light'"
            :footer-bg-variant="'danger'"
            >
            <p>Deseja realmente excluir essa matrícula?</p>
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
            students: [],
            registrations: [],
            registration:{
                id: null,
                student_id: "",
                courses_id: [],
            },
            data:{
                courses: [],
                student: "",
                course: ""
            },
            selectedID: 0,
            pagination:{
                currentPage: 1,
                perPage: 10,
                totalRecords: 1,
            },
            fields: [
                { key: "edtExc", label: " " },
                //{ key: "id", label: "Código", sortable: true, class: "text-center" },
                { key: "name", label: "Aluno", sortable: true, class: "text-center" },
                { key: "courses", label: "Cursos", sortable: true, class: "text-center",
                    formatter: value => value.map(course => {
                        return this.courses.filter(c => c.id == course)[0].title;
                    }).join(", ")
                },
            ],
            busy: false,
        }
    },
    created(){
        this.busy = true;
        this.list();
        this.getRegistrationData();
    },
    methods:{
        list(){
            axios("registrations/allRegistrations").then(response => {
                let resp = response.data;
                if (resp.success){
                    this.registrations = resp.registrations;
                    this.pagination.totalRecords = this.registrations.length;
                }
                this.busy = false;
            }).catch(error => {
                console.error("Erro ao listar as matrículas. "+error);
            });
        },
        getRegistrationData(){
            axios("registrations/getRegistrationsData").then(response => {
                let resp = response.data;
                if (resp.success){
                    this.courses = resp.courses;
                    this.students = resp.students;
                }
            }).catch(error => {
                console.error("Erro ao buscar os dados das matrículas. "+error);
            });
        },
        addCourse(){
            this.data.courses.push(this.data.course);
            this.data.course = "";
        },
        removeCourse(id){
            this.data.courses.splice(id, 1);
        },
        save(){
            this.registration.student_id = this.students.filter(std => std.name == this.data.student)[0]["id"];
            this.data.courses.forEach(dataCourse => {
                this.registration.courses_id.push(this.courses.filter(course => course.title == dataCourse)[0]["id"]);
            });
            if (this.registration.id != null){
                this.update();
                return false;
            }

            axios.post("registrations", {
                data: this.registration
            }).then(response => {
                let resp = response.data;
                if (resp.success){
                    this.list();
                    this.$bvModal.hide("add-registration");
                    this.cleanFields();
                }
            }).catch(error => {
                console.error("Erro ao cadastrar o aluno. "+error);
            });
        },
        edit(id){
            this.selectedID = id;
            this.registration.id = id;
            axios.get(`registrations/${id}/edit`,
            { params: { id } }).then(response => {
                let resp = response.data;
                if (resp.success){
                   this.data.student = this.students.filter(std => std.id == resp.student)[0].name;
                   this.data.courses = resp.courses.map(course => {
                       return this.courses.filter(c => c.id == course)[0].title;
                   });
                   this.$bvModal.show("add-registration");
                }
            }).catch(error => {
                console.error("Erro ao buscar dados do aluno. "+error);
            });
        },
        update(){
            axios.put(`registrations/${this.registration.id}`,
            { id: this.registration.id, data: this.registration } ).then(response => {
                let resp = response.data;
                if (resp.success){
                    this.cleanFields();
                    this.$bvModal.hide("add-registration");
                    this.list();
                }
            }).catch(error => {
                console.error("Erro ao editar o aluno. "+error);
            });
        },
        remove(confirmation, id = null){
            if (!confirmation){
                this.selectedID = id;
                this.$bvModal.show("delete-registration");
                return false;
            }

            axios.delete(`registrations/${this.selectedID}`,
            { params: { id: this.selectedID} }).then(response => {
                let resp = response.data;
                if (resp.success) this.list();
                
                this.$bvModal.hide("delete-registration");
            }).catch(error => {
                console.error("Erro ao remover o aluno. "+error);
            });
        },
        cleanFields(){
            this.data.courses = [];
            this.data.student = "";
            this.data.course = "";
            this.registration.id =  null;
            this.registration.student_id =  "";
            this.registration.courses_id = [];
        }
    },
    watch:{
        students(value){
            if (value.length == 0)
                this.data.student = "Nenhum aluno cadastrado";
        },
        courses(value){
            if (value.length == 0)
                this.data.course = "Nenhum curso cadastrado";
        }
    }

}
</script>