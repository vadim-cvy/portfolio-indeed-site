<div class="pjs-filter__items">
  <div
    class="pjs-filter__job-card"
    v-for="(job, jobIndex) in jobs"
    :key="jobIndex"
    @click="() => openJobDetails( jobIndex )"
  >
    {{ job.title }}
  </div>
</div>