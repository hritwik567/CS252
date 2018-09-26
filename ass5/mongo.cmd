db.cases.aggregate([
    {"$group" : {
        _id:"$DISTRICT",
        count:{$sum:1}
        }
     },
     { $sort : { count : -1 } }

])

db.cases.aggregate([
    {"$match": {"Status": "Pending"} },
    { "$group" : { _id:"$DISTRICT", count: { $sum: 1 } } },
    { $sort : { count : -1 } }
])

db.cases.aggregate([
    {"$unwind": "$Act_Section" },
    {"$group" : { _id:"$Act_Section", count: { $sum: 1 } } },
    { $sort : { count : -1 } }
])
