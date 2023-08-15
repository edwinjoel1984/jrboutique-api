<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderResource;
use App\Models\ArticleSize;
use App\Models\Commitment;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return $this->sendResponse(OrderResource::collection($orders), 'Orders retrieved successfully.');
    }

    public function store(Request $request)
    {
        //
        $input = $request->all();
        $validator = Validator::make($input, [
            'order_date' => 'required',
            'customer_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $order = Order::create($input);


        return $this->sendResponse(new OrderResource($order), 'Order created successfully.');
    }

    public function show(Order $order)
    {
        return $this->sendResponse(new OrderResource($order), 'Order retrieved successfully.');
    }


    public function orders_by_status(Request $request)
    {
        $status = $request->all()['status'];
        $orders = Order::status($status)->get();
        return $this->sendResponse(OrderResource::collection($orders), 'Orders retrieved successfully.');
    }

    public function add_product_to_order(Request $request, $order_id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'article_size_id' => 'required',
            'unit_price' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input['order_id'] = $order_id;
        OrderDetail::create($input);
        $order = Order::find($order_id);

        return $this->sendResponse(new OrderResource($order), 'Product added successfully.');
    }

    public function update_detail(Request $request, $order_id, $order_detail_id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'unit_price' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input['order_id'] = $order_id;
        $order = Order::find($order_id);
        $orderDetail = OrderDetail::find($order_detail_id);
        $orderDetail['unit_price'] = $input['unit_price'];
        $orderDetail['quantity'] = $input['quantity'];

        $orderDetail->save();

        return $this->sendResponse(new OrderResource($order), 'Product updated successfully.');
    }

    public function remove_detail_item(Request $request, $order_id, $order_detail_id)
    {
        $order = Order::find($order_id);
        OrderDetail::destroy($order_detail_id);

        return $this->sendResponse(new OrderResource($order), 'Product removed successfully.');
    }

    public function confirm_order(Request $request, $order_id)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'order_date' => 'required',
                'customer_id' => 'required',
                'first_payment' => 'required',
                'payment_method' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $order = Order::find($order_id);
            // if ($order['status'] === 'CLOSED') {
            //     return $this->sendError('Order has already been closed.', [], 422);
            // }

            //Update Order 
            $order['customer_id'] = $input['customer_id'];
            $order['order_date'] = $input['order_date'];
            $order['status'] = $input['status'];
            $order->save();

            //Update Inventory 
            $items = OrderDetail::where('order_id', $order_id)->get();
            foreach ($items as $item) {
                $articleSize = ArticleSize::find($item['article_size_id']);
                $articleSize['quantity'] = $articleSize['quantity'] - $item['quantity'];
                $transactionData = ["article_size_id" => $item['article_size_id'], "order_id" => $order_id, "customer_id" => $order['customer_id'],  "type" => "Venta", "quantity" => $item['quantity'], "memo" => "Venta Order #" . $order_id];
                $articleSize->save();
                Transaction::create($transactionData);
            }

            // Transaction::insert($transactionsByOrder);

            //Create Commitment
            if ($order['payment_method'] === 'CREDITO') {
                $total_order = $order->total_order($order_id);
                $amount = $input['first_payment'] ? $total_order - $input['first_payment_amount'] : $total_order;
                $commitmentData = ["date" => $order['order_date'], "customer_id" => $order['customer_id'], "total_amount" => $total_order, "pending_amount" => $amount, "order_id" => $order_id, "memo" => "Venta Order #" . $order_id];
                Commitment::create($commitmentData);
            }

            DB::commit();
            return $this->sendResponse(new OrderResource($order), 'Order confirmed successfully.');
        } catch (\Exception  $e) {
            DB::rollback();
            return $this->sendError('Something went wrong.', $e, 422);
        }
    }
}
