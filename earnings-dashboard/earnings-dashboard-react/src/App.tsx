import "./App.css";
import { useState } from "react";
import Form, { TutorEarningsI } from "./components/Form";
import Table from "./components/Table";

export interface TutorEarningI {
  cat_id: string;
  cat_name: string;
  orders_count: number;
}

export interface DataI {
  tutor_id: string;
  tutor_name: string;
  tutor_email: string;
  month: string;
  year: string;
  tutor_earnings: TutorEarningI[];
}

function App() {
  const [data, setData] = useState<DataI | null>(null);
  const [isPending, setIsPending] = useState(false);
  const [error, setError] = useState<string>("");

  const getTutorEarnings = async ({
    tutor_id,
    tutor_name,
    tutor_email,
    month,
    year,
  }: TutorEarningsI) => {
    setIsPending(true);
    try {
      const data = await (
        await fetch(
          `https://api.eliteacademyeg.com/wp-json/elite/v1/get-tutor-earnings?tutor_id=${tutor_id}&tutor_name=${tutor_name}&tutor_email=${tutor_email}&month=${month}&year=${year}`
        )
      ).json();

      setData(data);
      setError("");
    } catch (error) {
      const err = error as Error;
      console.error(err);
      setError(err.message);
    } finally {
      setIsPending(false);
    }
  };

  return (
    <>
      <Form getTutorEarnings={getTutorEarnings} error={error} />
      <Table data={data} isPending={isPending} />
    </>
  );
}

export default App;
