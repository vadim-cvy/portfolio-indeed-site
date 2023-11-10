<!-- todo: replace with loading cards animation -->
<div v-if="isLoading">
  Loading...
</div>

<div class="pjs-filter__items" v-else-if="matches.searchTerm">
  <div
    class="pjs-filter__job-card"
    v-for="job in matches.jobs"
    :key="job.id"
    @click="() => jobDetailsBox.toggle( job )"
  >
    {{ job.title }}
  </div>
</div>