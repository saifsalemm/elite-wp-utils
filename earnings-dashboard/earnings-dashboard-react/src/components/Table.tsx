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
          <table className="rev-table">
            <thead>
              <tr className="rev-tr">
                <th className="rev-th">القسم</th>
                <th className="rev-th">الأكواد المستخدمة</th>
                <th className="rev-th">المبلغ</th>
              </tr>
            </thead>
            <tbody>
              <tr className="rev-tr">
                <td className="rev-td">تكلفة السرفر</td>
                <td className="rev-td">ـــ</td>
                <td className="rev-td">300</td>
              </tr>
              {data.tutor_earnings.map((item) => (
                <tr className="rev-tr" key={item.cat_id}>
                  <td className="rev-td">{item.cat_name}</td>
                  <td className="rev-td">{item.orders_count}</td>
                  <td className="rev-td">{item.orders_count * 5}</td>
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
