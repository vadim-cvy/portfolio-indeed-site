<div class="pjs-filter__secondary-controls">
  <div
    class="pjs-filter__secondary-controls__control"
    v-for="(control, controlIndex) in controls.secondary"
    :key="controlIndex"
  >
    <span class="pjs-filter__secondary-controls__control__label">
      {{ control.label }}
    </span>

    <div class="pjs-filter__secondary-controls__control__options">
      <a
        href="#"
        v-for="(option, optionIndex) in control.options"
        :key="optionIndex"
        @click="() => control.val = option.val"
      >
        {{ option.label }}
      </a>
    </div>
  </div>
</div>