import { useEffect, useRef, useState } from "react";

export const monthsMapper = {
  "01": "يناير",
  "02": "فبراير",
  "03": "مارس",
  "04": "ابريل",
  "05": "مايو",
  "06": "يونيو",
  "07": "يوليو",
  "08": "اغسطس",
  "09": "سبتمبر",
  "10": "اكتوبر",
  "11": "نوفمبر",
  "12": "ديسمبر",
};

export interface TutorEarningsI {
  tutor_id: string;
  tutor_name: string;
  tutor_email: string;
  month: string;
  year: string;
}

const Form = ({
  getTutorEarnings,
  error,
}: {
  getTutorEarnings: ({
    tutor_id,
    tutor_name,
    tutor_email,
    month,
    year,
  }: TutorEarningsI) => void;
  error: string;
}) => {
  const tutorRef = useRef<HTMLSelectElement>(null);
  const monthRef = useRef<HTMLSelectElement>(null);
  const yearRef = useRef<HTMLInputElement>(null);

  const [tutorData, setTutorData] = useState<
    { id: string; name: string; email: string }[] | null
  >(null);

  const getTutors = async () => {
    try {
      const tutors = await (
        await fetch("/wp-json/elite/v1/get-tutors-for-fees")
      ).json();

      setTutorData(tutors);
    } catch (e) {
      setTutorData([]);
    }
  };

  useEffect(() => {
    getTutors();
  }, []);

  return (
    <div className="fees_container">
      <label htmlFor="tutor">Select Tutor: </label>
      <br />
      <select ref={tutorRef} className="fees_select" name="tutor_id" required>
        {tutorData?.map((item) => (
          <option value={item.id}>{`${item.name} - ${item.email}`}</option>
        ))}
      </select>
      <br />

      <label htmlFor="month_fees">Select Month: </label>
      <br />
      <select ref={monthRef} className="fees_select" name="month_fees" required>
        {Object.keys(monthsMapper).map((key) => (
          <option value={key}>
            {monthsMapper[key as keyof typeof monthsMapper]}
          </option>
        ))}
      </select>
      <br />

      <label htmlFor="month_fees">Select Year: </label>
      <input
        ref={yearRef}
        className="fees_select"
        type="number"
        name="year"
        required
      />
      <br />

      <button
        onClick={() => {
          const tutor = tutorData?.filter(
            (item) => String(item.id) === String(tutorRef?.current?.value!)
          )[0];
          getTutorEarnings({
            tutor_id: tutor?.id!,
            tutor_name: tutor?.name!,
            tutor_email: tutor?.email!,
            month: monthRef.current?.value ?? "",
            year: yearRef.current?.value ?? "",
          });
        }}
        type="button"
        className="submit_fees_report"
        name="submit_fees_report"
      >
        Get Report
      </button>
      <br />
      <div className="updates-msg" style={{ display: "none" }}>
        {error && <p className="tutor-error-msg">{error}</p>}
      </div>
      <span className="elite-spinner" style={{ display: "none" }}></span>
    </div>
  );
};

export default Form;
