import { DataI } from "../App";
import { monthsMapper } from "./Form";

const Table = ({
  data,
  isPending,
}: {
  data: DataI | null;
  isPending: boolean;
}) => {
  if (isPending)
    return (
      <div className="rev-spinner-div">
        <div className="rev-spinner"></div>
      </div>
    );

  return (
    <>
      {data && (
        <div className="rev-container" dir="rtl">
          <h2 className="tutor-name">{`تقرير أ. ${data?.tutor_name} عن شهر ${
            monthsMapper[data?.month as keyof typeof monthsMapper]
          }`}</h2>
          <table>
            <thead>
              <tr>
                <th>القسم</th>
                <th>الأكواد المستخدمة</th>
                <th>المبلغ</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>تكلفة السرفر</td>
                <td>ـــ</td>
                <td>300</td>
              </tr>
              {data.tutor_earnings.map((item) => (
                <tr key={item.cat_id}>
                  <td>{item.cat_name}</td>
                  <td>{item.orders_count}</td>
                  <td>{item.orders_count * 5}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </>
  );
};

export default Table;
