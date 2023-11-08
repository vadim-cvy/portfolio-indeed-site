<div class="pjs-filter__items">
  <div
    class="pjs-filter__job-card"
    v-for="job in matches"
    :key="job.id"
    @click="() => jobDetailsBox.toggle( job )"
  >
    {{ job.title }}
  </div>
</div>