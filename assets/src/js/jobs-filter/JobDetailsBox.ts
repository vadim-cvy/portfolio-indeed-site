import Job from "./Job"

class JobDetailsBox
{
  public get isVisible()
  {
    return !! this.job
  }

  private _job: Job | null = null

  public get job()
  {
    return this._job
  }

  public toggle( job: Job | null = null )
  {
    if ( job === this.job )
    {
      job = null
    }

    this._job = job
  }
}

export default new JobDetailsBox()