<script setup>
import {ref} from 'vue'
import HomeTLB from "~/components/Icons/Home-TLB.vue"
import PhoneTLB from "~/components/Icons/Phone-TLB.vue"
import MailTLB from "~/components/Icons/Mail-TLB.vue"
import UserMaleTLB from "~/components/Icons/UserMale-TLB.vue"
import CardTLB from "~/components/Icons/Card-TLB.vue"
import CheckTLB from "~/components/Icons/Check-TLB.vue"
import MonnaieTLB from "~/components/Icons/Monnaie-TLB.vue"
import UserFemaleTLB from "~/components/Icons/UserFemale-TLB.vue"
import PaiementEmpty from "~/components/Icons/Paiement-Empty.vue"
import CommentEmpty from "~/components/Icons/Comment-Empty.vue"
import ResponsableTLB from "~/components/Icons/Responsable-TLB.vue"
import PlusLight from "~/components/Icons/PlusLight.vue"
import AddResponsableModal from "~/components/modals/AddResponsableModal.vue";
import SaveButton from "~/components/form/SaveButton.vue";
import AddElevesModal from "~/components/modals/AddElevesModal.vue";

const showAddStudentsModal = ref(false)

const handleAddStudents = (newStudents) => {
}

const showAddResponsableModal = ref(false)
const isEditing = ref(false)
const contactInfo = ref({
  name: 'Abdoullah Mohammed',
  phone: '0612345678',
  email: 'abdoullah.mohammed@gmail.com',
  address: '7 rue de la piété, 75020 Paris'
})

const editForm = ref({...contactInfo.value})

const handleEdit = () => {
  isEditing.value = true
  editForm.value = {...contactInfo.value}
}

const handleSave = () => {
  contactInfo.value = {...editForm.value}
  isEditing.value = false
}

const handleAddResponsable = (newResponsable) => {
}

const comments = ref([
  {
    id: 1,
    author: 'Pr. Omar',
    date: '05/12/2024',
    content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
  },
])

const newComment = ref('')
const commentsContainer = ref(null)

const scrollToBottom = () => {
  nextTick(() => {
    if (commentsContainer.value) {
      commentsContainer.value.scrollTop = commentsContainer.value.scrollHeight
    }
  })
}

const handleCommentSubmit = () => {
  if (newComment.value.trim()) {
    const comment = {
      id: comments.value.length + 1,
      author: 'Pr. Omar',
      date: new Date().toLocaleDateString(),
      content: newComment.value.trim()
    }
    comments.value.push(comment)
    newComment.value = ''
    scrollToBottom()
  }
}

onMounted(() => {
  scrollToBottom()
})

definePageMeta({
  layout: 'auth',
  layoutData: {
    title: 'Famille'
  }
})
</script>

<template>
  <div class="flex flex-col w-full pt-3 px-10 font-montserrat">
    <NuxtLink
        :to="{
    name: 'family-id-classes',
    params: { id: $route.params.id }
  }"
        class="bg-default text-white px-4 py-2 w-fit rounded-lg hover:opacity-90 inline-flex items-center justify-between gap-x-2 ml-auto">
      <span>Choix des classes</span>
    </NuxtLink>
    <div class="grid grid-cols-3 gap-x-20 w-full mt-2">
      <div class="flex flex-col gap-y-6 col-span-1 bg-white py-4 px-10 border rounded-3xl relative">
        <div class="bg-gray-light mr-auto pl-6 pr-8 font-bold font-montserrat text-xl text-center py-2 rounded-xl">
          {{ contactInfo.name }}
        </div>

        <button
            @click="showAddResponsableModal = true"
            class="absolute right-3 top-6 inline-flex items-center justify-center hover:opacity-80 rounded-full hover:bg-gray-50 p-1"
        >
          <PlusLight class="size-6"/>
        </button>

        <AddResponsableModal
            :is-open="showAddResponsableModal"
            @close="showAddResponsableModal = false"
            @save="handleAddResponsable"
        />

        <div class="flex flex-col justify-center gap-y-0.5 text-default">
          <div class="text-black ml-auto text-sm font-sans cursor-pointer" v-if="!isEditing">
            <button
                class="underline"
                @click="handleEdit"
            >
              Modifier
            </button>
          </div>
          <div class="font-bold text-base mb-1">Contact</div>
          <div class="inline-flex items-center gap-x-2 text-xs mb-1 font-medium">
            <PhoneTLB/>
            <template v-if="!isEditing">
              <span>{{ contactInfo.phone }}</span>
            </template>
            <input
                v-else
                v-model="editForm.phone"
                type="tel"
                class="w-full p-1 border rounded focus:border-default focus:ring-0 focus:outline-none"
            >
          </div>
          <div class="inline-flex items-center gap-x-2 text-xs font-medium">
            <MailTLB/>
            <template v-if="!isEditing">
              <span>{{ contactInfo.email }}</span>
            </template>
            <input
                v-else
                v-model="editForm.email"
                type="email"
                class="w-full p-1 border rounded focus:border-default focus:ring-0 focus:outline-none"
            >
          </div>
        </div>

        <div class="w-full border rounded-xl bg-[#d9d9D9]"></div>
        <div class="flex flex-col justify-center gap-y-0.5">
          <div class="font-bold text-base mb-1">Adresse</div>
          <div class="inline-flex items-center gap-x-2 font-montserrat text-xs text-default font-medium">
            <HomeTLB/>
            <template v-if="!isEditing">
              <span>{{ contactInfo.address }}</span>
            </template>
            <input
                v-else
                v-model="editForm.address"
                type="text"
                class="w-full p-1 border rounded focus:border-default focus:ring-0 focus:outline-none"
            >
          </div>
        </div>

        <div class="flex justify-center" v-if="isEditing">
          <SaveButton
              @click="handleSave"
          >
            Enregistrer
          </SaveButton>
        </div>
      </div>

      <AddElevesModal
          :is-open="showAddStudentsModal"
          @close="showAddStudentsModal = false"
          @save="handleAddStudents"
      />
      <div class="grid grid-rows-5 w-full py-4 px-10 col-span-2 bg-white rounded-2xl divide-y border divide-[#E6EFF5] relative">

        <div class="grid grid-cols-12 font-bold font-montserrat">
          <div class="col-span-7 inline-flex items-center justify-start pl-10">Élève</div>
          <div class="col-span-2 inline-flex items-center justify-start">Classe</div>
          <div class="col-span-3 inline-flex items-center justify-start">Date d'inscription</div>
          <button
              @click="showAddStudentsModal = true"
              class="absolute right-4 top-8 inline-flex items-center justify-center hover:opacity-80 rounded-full hover:bg-gray-50 p-1"
          >
            <PlusLight class="size-6"/>
          </button>
        </div>
        <div class="grid grid-cols-12 font-nunito py-4">
          <div class="col-span-7 inline-flex items-center justify-start gap-x-2 pl-1">
            <div class="inline-flex items-center gap-x-3">
              <ResponsableTLB/>
              <UserMaleTLB/>
            </div>
            <span>Foulane Mohammed</span>
          </div>
          <div class="col-span-2 inline-flex items-center justify-start">3eC</div>
          <div class="col-span-3 inline-flex items-center justify-start">28 Jan, 12.30 AM</div>
        </div>
        <div class="grid grid-cols-12 font-nunito py-4">
          <div class="col-span-7 inline-flex items-center justify-start gap-x-2 pl-10">
            <UserMaleTLB/>
            <span>Bilal Mohammed</span>
          </div>
          <div class="col-span-2 inline-flex items-center justify-start">5eB</div>
          <div class="col-span-3 inline-flex items-center justify-start">25 Jan, 10.40 PM</div>
        </div>
        <div class="grid grid-cols-12 font-nunito py-4">
          <div class="col-span-7 inline-flex items-center justify-start gap-x-2 pl-10">
            <UserFemaleTLB/>
            <span>Fatima Mohammed</span>
          </div>
          <div class="col-span-2 inline-flex items-center justify-start">1eA</div>
          <div class="col-span-3 inline-flex items-center justify-start">20 Jan, 10.40 PM</div>
        </div>
        <div class="grid grid-cols-12 font-nunito py-4">
          <div class="col-span-7 inline-flex items-center justify-start gap-x-2 pl-10">
            <UserFemaleTLB/>
            <span>Khadija Mohammed</span>
          </div>
          <div class="col-span-2 inline-flex items-center justify-start">5eA</div>
          <div class="col-span-3 inline-flex items-center justify-start">15 Jan, 03.29 PM</div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-x-20 w-full h-[23rem] mt-2">
      <div class="flex flex-col gap-y-6 col-span-1 bg-white py-4 border rounded-3xl">
        <div class="font-bold text-2xl font-montserrat px-10">Paiements</div>
        <div class="flex flex-col divide-y divide-[#E6EFF5] px-6" v-if="0 !== true">
          <div class="grid grid-cols-12 px-4 py-3 font-nunito">
            <div class="col-span-1 inline-flex items-center justify-center">
              <CardTLB/>
            </div>
            <div class="col-span-7 inline-flex items-center pl-2">Foulane M.</div>
            <div class="col-span-3 inline-flex items-center">Arabe</div>
            <div class="col-span-1 inline-flex items-center">250€</div>
          </div>
          <div class="grid grid-cols-12 px-4 py-3 font-nunito">
            <div class="col-span-1 inline-flex items-center justify-center">
              <CheckTLB/>
            </div>
            <div class="col-span-7 inline-flex items-center pl-2">Fatima M.</div>
            <div class="col-span-3 inline-flex items-center">Coran</div>
            <div class="col-span-1 inline-flex items-center">150€</div>
          </div>
          <div class="grid grid-cols-12 px-4 py-3 font-nunito">
            <div class="col-span-1 inline-flex items-center justify-center">
              <MonnaieTLB/>
            </div>
            <div class="col-span-7 inline-flex items-center pl-2">Bilal M.</div>
            <div class="col-span-3 inline-flex items-center">Coran</div>
            <div class="col-span-1 inline-flex items-center">150€</div>
          </div>
        </div>
        <div class="inline-flex flex-col items-center justify-center gap-y-4 w-full" v-else>
          <PaiementEmpty width="250" height="200"/>
          <div class="font-montserrat text-base text-placeholder font-medium">Aucun paiement</div>
        </div>
      </div>

      <div class="flex flex-col col-span-1 bg-white py-4 px-10 rounded-3xl border text-default h-[23rem]">
        <div class="font-bold text-2xl font-montserrat mb-6">Commentaires</div>
        <div
            ref="commentsContainer"
            class="flex flex-col gap-y-6 overflow-y-auto flex-1 mb-4"
            v-if="comments.length"
        >
          <div
              v-for="comment in comments"
              :key="comment.id"
              class="bg-gray-light w-full flex flex-col gap-y-2 px-6 py-2 shadow-sm rounded-lg"
          >
            <div class="flex items-center justify-between font-montserrat text-xs">
              <div class="font-bold">{{ comment.author }}</div>
              <div class="font-light">{{ comment.date }}</div>
            </div>
            <div class="text-xs font-montserrat">{{ comment.content }}</div>
          </div>
        </div>
        <div class="flex flex-col items-center justify-center gap-y-4 flex-1" v-else>
          <CommentEmpty width="200" height="200"/>
          <div class="font-montserrat text-base text-placeholder font-medium">Aucun commentaire</div>
        </div>

        <form @submit.prevent="handleCommentSubmit" class="flex gap-x-4 items-center justify-center">
      <textarea
          v-model="newComment"
          placeholder="Écrivez votre commentaire..."
          rows="3"
          class="flex-1 p-3 text-xs font-montserrat border rounded-lg resize-none focus:outline-none focus:ring-1 focus:ring-default"
      ></textarea>
          <button
              type="submit"
              class="px-5 py-2 bg-default text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-nunito"
          >
            Ajouter
          </button>
        </form>
      </div>
    </div>
  </div>
</template>