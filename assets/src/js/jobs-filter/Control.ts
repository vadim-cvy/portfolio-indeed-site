import ControlOption from "./ControlOption"

export default class Control
{
  public constructor(
    private readonly name: string,
    public readonly label: string,
    public readonly options: ControlOption[],
  ) {}

  private _selectedOption: ControlOption | null = null

  public get selectedOption() : ControlOption | null
  {
    return this._selectedOption
  }

  public toggleSelection( option: ControlOption )
  {
    this._selectedOption = this.selectedOption !== option ? option : null
  }
}