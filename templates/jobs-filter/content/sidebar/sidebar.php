<div class="pjs-filter__content__column-sidebar">
  <div class="pjs-filter__job-card" v-if="jobDetailsBox.isVisible">
    <a
      href="#"
      @click="() => jobDetailsBox.toggle()"
    >
      Close
    </a>

    {{ jobDetailsBox.job.title }}
  </div>
</div>